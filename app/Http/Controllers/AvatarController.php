<?php

namespace App\Http\Controllers;

use App\Models\Avatar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

class AvatarController extends Controller
{
    /**
     * Base path inside public/ where avatar images live.
     * URL will be: asset('public/storage/avatars/...')
     */
    private function avatarDir(string $sub = ''): string
    {
        return public_path('storage/avatars' . ($sub ? '/' . $sub : ''));
    }

    /**
     * Avatar gallery — my avatars + public avatars
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $myAvatars = Avatar::ownedBy($userId)->latest()->get();

        $publicQuery = Avatar::public()
            ->where('user_id', '!=', $userId)
            ->latest();

        $tag = $request->get('tag');
        if ($tag && $tag !== 'All') {
            $publicQuery->whereJsonContains('tags', $tag);
        }

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
            'image'  => 'required|image|mimes:jpeg,jpg,png,webp|max:10240',
            'gender' => 'nullable|in:male,female,neutral',
            'tags'   => 'nullable|array',
            'tags.*' => 'string|max:50',
            'is_public' => 'nullable|boolean',
        ]);

        $file     = $request->file('image');
        $filename = 'avatar_' . Auth::id() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        // Ensure directories exist
        File::ensureDirectoryExists($this->avatarDir());
        File::ensureDirectoryExists($this->avatarDir('thumbs'));

        // Save original directly into public/storage/avatars/
        $file->move($this->avatarDir(), $filename);

        // Generate 300×300 thumbnail
        $thumbFilename = 'thumb_' . $filename;
        $manager = new ImageManager(new GdDriver());
        $thumbnail = $manager->read($this->avatarDir() . '/' . $filename);
        $thumbnail->cover(300, 300);
        $thumbnail->save($this->avatarDir('thumbs') . '/' . $thumbFilename);

        $avatar = Avatar::create([
            'user_id'        => Auth::id(),
            'name'           => $request->name,
            'image_path'     => 'avatars/' . $filename,
            'thumbnail_path' => 'avatars/thumbs/' . $thumbFilename,
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

        if ($request->hasFile('image')) {
            // Delete old files
            $oldImage = $this->avatarDir() . '/' . basename($avatar->image_path);
            $oldThumb = $this->avatarDir('thumbs') . '/' . basename($avatar->thumbnail_path);
            if (file_exists($oldImage)) unlink($oldImage);
            if (file_exists($oldThumb)) unlink($oldThumb);

            $file     = $request->file('image');
            $filename = 'avatar_' . Auth::id() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            File::ensureDirectoryExists($this->avatarDir());
            File::ensureDirectoryExists($this->avatarDir('thumbs'));

            $file->move($this->avatarDir(), $filename);

            $thumbFilename = 'thumb_' . $filename;
            $manager = new ImageManager(new GdDriver());
            $thumbnail = $manager->read($this->avatarDir() . '/' . $filename);
            $thumbnail->cover(300, 300);
            $thumbnail->save($this->avatarDir('thumbs') . '/' . $thumbFilename);

            $avatar->image_path     = 'avatars/' . $filename;
            $avatar->thumbnail_path = 'avatars/thumbs/' . $thumbFilename;
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

        $oldImage = $this->avatarDir() . '/' . basename($avatar->image_path);
        $oldThumb = $this->avatarDir('thumbs') . '/' . basename($avatar->thumbnail_path);
        if (file_exists($oldImage)) unlink($oldImage);
        if (file_exists($oldThumb)) unlink($oldThumb);

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
