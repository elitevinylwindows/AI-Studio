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

    /* ────────────────────────────────────────────────
     *  INDEX
     * ──────────────────────────────────────────────── */
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

    /* ────────────────────────────────────────────────
     *  CREATE (show form)
     * ──────────────────────────────────────────────── */
    public function create()
    {
        return view('avatar.create');
    }

    /* ────────────────────────────────────────────────
     *  STORE  — fast: save original + DB row
     *  If style is 'realistic' the avatar is ready immediately.
     *  If cartoon/3d the blade will fire an AJAX call to /transform.
     * ──────────────────────────────────────────────── */
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

        // Ensure directories
        File::ensureDirectoryExists($this->avatarDir());
        File::ensureDirectoryExists($this->avatarDir('originals'));
        File::ensureDirectoryExists($this->avatarDir('thumbs'));

        // Save the original upload
        $origFilename = 'orig_' . $uid . '_' . $uniq . '.' . $file->getClientOriginalExtension();
        $file->move($this->avatarDir('originals'), $origFilename);
        $origFullPath = $this->avatarDir('originals') . '/' . $origFilename;

        // For realistic — copy as the final image right away
        // For cartoon/3d — also use the original as a temporary placeholder
        $finalFilename = 'avatar_' . $uid . '_' . $uniq . '.png';
        copy($origFullPath, $this->avatarDir() . '/' . $finalFilename);

        // Thumbnail from the original (will be regenerated after transform)
        $thumbFilename = 'thumb_' . $uid . '_' . $uniq . '.png';
        $manager = new ImageManager(new GdDriver());
        $thumb   = $manager->read($this->avatarDir() . '/' . $finalFilename);
        $thumb->cover(300, 300);
        $thumb->save($this->avatarDir('thumbs') . '/' . $thumbFilename);

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
            'status'              => ($style === 'realistic') ? 'active' : 'processing',
        ]);

        // For realistic, redirect straight to index
        if ($style === 'realistic') {
            return redirect()
                ->route('avatar.index')
                ->with('success', "Avatar \"{$avatar->name}\" created.");
        }

        // For cartoon/3d, redirect to a "processing" page that fires AJAX
        return redirect()
            ->route('avatar.show', $avatar)
            ->with('transform', true);  // flag tells the show view to auto-trigger AJAX
    }

    /* ────────────────────────────────────────────────
     *  TRANSFORM  — AJAX endpoint (slow AI work)
     *  POST /avatar/{avatar}/transform
     *  Returns JSON {status, image_url, error?}
     * ──────────────────────────────────────────────── */
    public function transform(Avatar $avatar)
    {
        $this->authorizeOwner($avatar);

        // Bump PHP limits for this request
        set_time_limit(120);
        ini_set('max_execution_time', '120');

        if ($avatar->style === 'realistic' || $avatar->status === 'active') {
            return response()->json([
                'status'    => 'already_done',
                'image_url' => $avatar->image_url,
            ]);
        }

        $origPath = $this->avatarDir('originals') . '/' . basename($avatar->original_image_path);

        if (!file_exists($origPath)) {
            return response()->json(['status' => 'error', 'error' => 'Original image not found.'], 422);
        }

        try {
            $content = $this->transformService->transform($origPath, $avatar->style);

            if (!$content) {
                $avatar->update(['status' => 'failed']);
                return response()->json(['status' => 'error', 'error' => 'AI transformation returned no result.'], 422);
            }

            // Overwrite the avatar image
            $avatarFullPath = $this->avatarDir() . '/' . basename($avatar->image_path);
            file_put_contents($avatarFullPath, $content);

            // Regenerate thumbnail
            $thumbFullPath = $this->avatarDir('thumbs') . '/' . basename($avatar->thumbnail_path);
            $manager = new ImageManager(new GdDriver());
            $thumb   = $manager->read($avatarFullPath);
            $thumb->cover(300, 300);
            $thumb->save($thumbFullPath);

            $avatar->update(['status' => 'active']);

            // Add cache-buster
            $bust = '?v=' . time();

            return response()->json([
                'status'        => 'ok',
                'image_url'     => $avatar->image_url . $bust,
                'thumbnail_url' => $avatar->thumbnail_url . $bust,
            ]);

        } catch (\Throwable $e) {
            Log::error('Avatar AI transform failed', [
                'avatar_id' => $avatar->id,
                'error'     => $e->getMessage(),
            ]);

            $avatar->update(['status' => 'failed']);

            return response()->json([
                'status' => 'error',
                'error'  => $e->getMessage(),
            ], 422);
        }
    }

    /* ────────────────────────────────────────────────
     *  SHOW
     * ──────────────────────────────────────────────── */
    public function show(Avatar $avatar)
    {
        return view('avatar.show', compact('avatar'));
    }

    /* ────────────────────────────────────────────────
     *  EDIT
     * ──────────────────────────────────────────────── */
    public function edit(Avatar $avatar)
    {
        $this->authorizeOwner($avatar);
        return view('avatar.edit', compact('avatar'));
    }

    /* ────────────────────────────────────────────────
     *  UPDATE
     * ──────────────────────────────────────────────── */
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

            $this->deleteAvatarFiles($avatar);

            File::ensureDirectoryExists($this->avatarDir());
            File::ensureDirectoryExists($this->avatarDir('originals'));
            File::ensureDirectoryExists($this->avatarDir('thumbs'));

            $origFilename = 'orig_' . Auth::id() . '_' . $uniq . '.' . $file->getClientOriginalExtension();
            $file->move($this->avatarDir('originals'), $origFilename);
            $origFullPath = $this->avatarDir('originals') . '/' . $origFilename;

            $finalFilename = 'avatar_' . Auth::id() . '_' . $uniq . '.png';
            copy($origFullPath, $this->avatarDir() . '/' . $finalFilename);

            $thumbFilename = 'thumb_' . Auth::id() . '_' . $uniq . '.png';
            $manager = new ImageManager(new GdDriver());
            $thumb   = $manager->read($this->avatarDir() . '/' . $finalFilename);
            $thumb->cover(300, 300);
            $thumb->save($this->avatarDir('thumbs') . '/' . $thumbFilename);

            $avatar->style               = $style;
            $avatar->image_path          = 'avatars/' . $finalFilename;
            $avatar->original_image_path = 'avatars/originals/' . $origFilename;
            $avatar->thumbnail_path      = 'avatars/thumbs/' . $thumbFilename;
            $avatar->status              = ($style === 'realistic') ? 'active' : 'processing';
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

    /* ────────────────────────────────────────────────
     *  DESTROY
     * ──────────────────────────────────────────────── */
    public function destroy(Avatar $avatar)
    {
        $this->authorizeOwner($avatar);
        $this->deleteAvatarFiles($avatar);
        $avatar->delete();

        return redirect()
            ->route('avatar.index')
            ->with('success', 'Avatar deleted.');
    }

    /* ────────────────────────────────────────────────
     *  Helpers
     * ──────────────────────────────────────────────── */
    private function deleteAvatarFiles(Avatar $avatar): void
    {
        $files = [
            $this->avatarDir() . '/' . basename($avatar->image_path ?? ''),
            $this->avatarDir('thumbs') . '/' . basename($avatar->thumbnail_path ?? ''),
        ];

        if ($avatar->original_image_path) {
            $files[] = $this->avatarDir('originals') . '/' . basename($avatar->original_image_path);
        }

        foreach ($files as $f) {
            if ($f && file_exists($f)) unlink($f);
        }
    }

    private function authorizeOwner(Avatar $avatar): void
    {
        abort_unless($avatar->user_id === Auth::id(), 403, 'Not your avatar.');
    }
}
