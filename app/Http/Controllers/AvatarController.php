<?php

namespace App\Http\Controllers;

use App\Models\Avatar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

class AvatarController extends Controller
{
    /**
     * Avatar gallery — my avatars + public avatars
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        // My avatars
        $myAvatars = Avatar::ownedBy($userId)
            ->latest()
            ->get();

        // Public avatars (exclude own)
        $publicQuery = Avatar::public()
            ->where('user_id', '!=', $userId)
            ->latest();

        // Optional tag filter
        $tag = $request->get('tag');
        if ($tag && $tag !== 'All') {
            $publicQuery->whereJsonContains('tags', $tag);
        }

        // Optional search
        $q = trim($request->get('q', ''));
        if ($q !== '') {
            $publicQuery->where('name', 'like', "%{$q}%");
        }

        $publicAvatars = $publicQuery->get();

        $filters = ['All', 'Professional', 'Lifestyle', 'UGC', 'AI-generated', 'Community', 'Favorites'];

        return view('avatar.index', [
            'myAvatars'     => $myAvatars,
            'publicAvatars' => $publicAvatars,
            'filters'       => $filters,
            'activeTag'     => $tag ?: 'All',
            'q'             => $q,
        ]);
    }

    /**
     * Show create avatar form
     */
    public function create()
    {
        return view('avatar.create');
    }

    /**
     * Store a new avatar from uploaded image
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'image'  => 'required|image|mimes:jpeg,jpg,png,webp|max:10240', // 10 MB
            'gender' => 'nullable|in:male,female,neutral',
            'tags'   => 'nullable|array',
            'tags.*' => 'string|max:50',
            'is_public' => 'nullable|boolean',
        ]);

        // Store original image
        $file     = $request->file('image');
        $filename = 'avatar_' . Auth::id() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path     = $file->storeAs('avatars', $filename, 'public');

        // Generate thumbnail (300×300 cropped)
        $thumbFilename = 'thumb_' . $filename;
        $thumbPath     = 'avatars/thumbs/' . $thumbFilename;

        Storage::disk('public')->makeDirectory('avatars/thumbs');

        $manager   = new ImageManager(new GdDriver());
        $thumbnail = $manager->read($file->getRealPath());
        $thumbnail->cover(300, 300);
        $thumbnail->save(Storage::disk('public')->path($thumbPath));

        $avatar = Avatar::create([
            'user_id'        => Auth::id(),
            'name'           => $request->name,
            'image_path'     => $path,
            'thumbnail_path' => $thumbPath,
            'gender'         => $request->gender,
            'tags'           => $request->tags ?? [],
            'is_public'      => $request->boolean('is_public', false),
            'status'         => 'active',
        ]);

        return redirect()
            ->route('avatar.index')
            ->with('success', "Avatar \"{$avatar->name}\" created successfully.");
    }

    /**
     * Show single avatar detail
     */
    public function show(Avatar $avatar)
    {
        return view('avatar.show', compact('avatar'));
    }

    /**
     * Show edit form
     */
    public function edit(Avatar $avatar)
    {
        $this->authorizeOwner($avatar);
        return view('avatar.edit', compact('avatar'));
    }

    /**
     * Update avatar details (name, tags, gender, replace image)
     */
    public function update(Request $request, Avatar $avatar)
    {
        $this->authorizeOwner($avatar);

        $request->validate([
            'name'      => 'required|string|max:255',
            'image'     => 'nullable|image|mimes:jpeg,jpg,png,webp|max:10240',
            'gender'    => 'nullable|in:male,female,neutral',
            'tags'      => 'nullable|array',
            'tags.*'    => 'string|max:50',
            'is_public' => 'nullable|boolean',
        ]);

        // Replace image if a new one was uploaded
        if ($request->hasFile('image')) {
            // Delete old files
            Storage::disk('public')->delete($avatar->image_path);
            if ($avatar->thumbnail_path) {
                Storage::disk('public')->delete($avatar->thumbnail_path);
            }

            $file     = $request->file('image');
            $filename = 'avatar_' . Auth::id() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path     = $file->storeAs('avatars', $filename, 'public');

            $thumbFilename = 'thumb_' . $filename;
            $thumbPath     = 'avatars/thumbs/' . $thumbFilename;
            Storage::disk('public')->makeDirectory('avatars/thumbs');

            $manager   = new ImageManager(new GdDriver());
            $thumbnail = $manager->read($file->getRealPath());
            $thumbnail->cover(300, 300);
            $thumbnail->save(Storage::disk('public')->path($thumbPath));

            $avatar->image_path     = $path;
            $avatar->thumbnail_path = $thumbPath;
        }

        $avatar->name      = $request->name;
        $avatar->gender    = $request->gender;
        $avatar->tags      = $request->tags ?? [];
        $avatar->is_public = $request->boolean('is_public', false);
        $avatar->save();

        return redirect()
            ->route('avatar.index')
            ->with('success', "Avatar \"{$avatar->name}\" updated.");
    }

    /**
     * Delete avatar and its images
     */
    public function destroy(Avatar $avatar)
    {
        $this->authorizeOwner($avatar);

        Storage::disk('public')->delete($avatar->image_path);
        if ($avatar->thumbnail_path) {
            Storage::disk('public')->delete($avatar->thumbnail_path);
        }

        $avatar->delete();

        return redirect()
            ->route('avatar.index')
            ->with('success', 'Avatar deleted.');
    }

    /**
     * Simple ownership check
     */
    private function authorizeOwner(Avatar $avatar): void
    {
        abort_unless($avatar->user_id === Auth::id(), 403, 'Not your avatar.');
    }
}
