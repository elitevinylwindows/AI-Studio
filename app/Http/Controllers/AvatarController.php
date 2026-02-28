<?php

namespace App\Http\Controllers;

use App\Models\Avatar;
use App\Services\AvatarTransformService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

class AvatarController extends Controller
{
    private AvatarTransformService $transformService;

    public function __construct(AvatarTransformService $transformService)
    {
        $this->transformService = $transformService;
    }

    /**
     * Base path inside public/ where avatar images live.
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
     * Store a new avatar from uploaded image, optionally transforming it.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'image'     => 'required|image|mimes:jpeg,jpg,png,webp|max:10240',
            'style'     => 'required|in:realistic,cartoon,3d',
            'gender'    => 'nullable|in:male,female,neutral',
            'tags'      => 'nullable|array',
            'tags.*'    => 'string|max:50',
            'is_public' => 'nullable|boolean',
        ]);

        $file  = $request->file('image');
        $style = $request->input('style', 'realistic');
        $uid   = Auth::id();
        $uniq  = uniqid();

        // Ensure directories exist
        File::ensureDirectoryExists($this->avatarDir());
        File::ensureDirectoryExists($this->avatarDir('originals'));
        File::ensureDirectoryExists($this->avatarDir('thumbs'));

        // 1. Always save the original upload
        $origFilename = 'orig_' . $uid . '_' . $uniq . '.' . $file->getClientOriginalExtension();
        $file->move($this->avatarDir('originals'), $origFilename);
        $origFullPath = $this->avatarDir('originals') . '/' . $origFilename;

        // 2. Transform based on style
        $finalFilename = 'avatar_' . $uid . '_' . $uniq . '.png';

        if ($style === 'realistic') {
            // No transformation — just copy original as the avatar
            copy($origFullPath, $this->avatarDir() . '/' . $finalFilename);
        } else {
            // Send to OpenAI for cartoon or 3D transformation
            try {
                $transformedContent = $this->transformService->transform($origFullPath, $style);

                if ($transformedContent) {
                    file_put_contents($this->avatarDir() . '/' . $finalFilename, $transformedContent);
                } else {
                    // Transformation failed — fall back to original
                    copy($origFullPath, $this->avatarDir() . '/' . $finalFilename);
                    session()->flash('warning', 'AI transformation failed. The original image was saved instead. You can retry by editing the avatar.');
                }
            } catch (\Throwable $e) {
                Log::error('Avatar transform error: ' . $e->getMessage());
                copy($origFullPath, $this->avatarDir() . '/' . $finalFilename);
                session()->flash('warning', 'AI transformation error: ' . $e->getMessage() . '. Original image saved.');
            }
        }

        // 3. Generate thumbnail from the final avatar image
        $thumbFilename = 'thumb_' . $uid . '_' . $uniq . '.png';
        $manager = new ImageManager(new GdDriver());
        $thumbnail = $manager->read($this->avatarDir() . '/' . $finalFilename);
        $thumbnail->cover(300, 300);
        $thumbnail->save($this->avatarDir('thumbs') . '/' . $thumbFilename);

        // 4. Save to DB
        $avatar = Avatar::create([
            'user_id'             => $uid,
            'name'                => $request->name,
            'style'               => $style,
            'image_path'          => 'avatars/' . $finalFilename,
            'original_image_path' => 'avatars/originals/' . $origFilename,
            'thumbnail_path'      => 'avatars/thumbs/' . $thumbFilename,
            'gender'              => $request->gender,
            'tags'                => $request->tags ?? [],
            'is_public'           => $request->boolean('is_public', false),
            'status'              => 'active',
        ]);

        return redirect()
            ->route('avatar.index')
            ->with('success', "Avatar \"{$avatar->name}\" created as {$style} style.");
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
            'style'     => 'nullable|in:realistic,cartoon,3d',
            'gender'    => 'nullable|in:male,female,neutral',
            'tags'      => 'nullable|array',
            'tags.*'    => 'string|max:50',
            'is_public' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $file  = $request->file('image');
            $style = $request->input('style', $avatar->style ?? 'realistic');
            $uniq  = uniqid();

            // Delete old files
            $this->deleteAvatarFiles($avatar);

            File::ensureDirectoryExists($this->avatarDir());
            File::ensureDirectoryExists($this->avatarDir('originals'));
            File::ensureDirectoryExists($this->avatarDir('thumbs'));

            // Save original
            $origFilename = 'orig_' . Auth::id() . '_' . $uniq . '.' . $file->getClientOriginalExtension();
            $file->move($this->avatarDir('originals'), $origFilename);
            $origFullPath = $this->avatarDir('originals') . '/' . $origFilename;

            // Transform
            $finalFilename = 'avatar_' . Auth::id() . '_' . $uniq . '.png';

            if ($style === 'realistic') {
                copy($origFullPath, $this->avatarDir() . '/' . $finalFilename);
            } else {
                try {
                    $content = $this->transformService->transform($origFullPath, $style);
                    if ($content) {
                        file_put_contents($this->avatarDir() . '/' . $finalFilename, $content);
                    } else {
                        copy($origFullPath, $this->avatarDir() . '/' . $finalFilename);
                    }
                } catch (\Throwable $e) {
                    Log::error('Avatar transform error: ' . $e->getMessage());
                    copy($origFullPath, $this->avatarDir() . '/' . $finalFilename);
                }
            }

            // Thumbnail
            $thumbFilename = 'thumb_' . Auth::id() . '_' . $uniq . '.png';
            $manager = new ImageManager(new GdDriver());
            $thumbnail = $manager->read($this->avatarDir() . '/' . $finalFilename);
            $thumbnail->cover(300, 300);
            $thumbnail->save($this->avatarDir('thumbs') . '/' . $thumbFilename);

            $avatar->style               = $style;
            $avatar->image_path          = 'avatars/' . $finalFilename;
            $avatar->original_image_path = 'avatars/originals/' . $origFilename;
            $avatar->thumbnail_path      = 'avatars/thumbs/' . $thumbFilename;
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
        $this->deleteAvatarFiles($avatar);
        $avatar->delete();

        return redirect()
            ->route('avatar.index')
            ->with('success', 'Avatar deleted.');
    }

    /**
     * Remove all image files associated with an avatar.
     */
    private function deleteAvatarFiles(Avatar $avatar): void
    {
        $files = [
            $this->avatarDir() . '/' . basename($avatar->image_path),
            $this->avatarDir('thumbs') . '/' . basename($avatar->thumbnail_path),
        ];

        if ($avatar->original_image_path) {
            $files[] = $this->avatarDir('originals') . '/' . basename($avatar->original_image_path);
        }

        foreach ($files as $f) {
            if ($f && file_exists($f)) unlink($f);
        }
    }

    /**
     * Simple ownership check
     */
    private function authorizeOwner(Avatar $avatar): void
    {
        abort_unless($avatar->user_id === Auth::id(), 403, 'Not your avatar.');
    }
}
