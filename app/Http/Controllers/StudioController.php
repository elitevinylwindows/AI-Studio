<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Project;

class StudioController extends Controller
{
    public function index(Request $req)
    {
        // Load or create a project (simple: 1 project per user/session for now)
        $project = Project::first() ?? Project::create([
            'title' => 'Cooking class',
            'state' => [
                'scenes' => [], // each: {id, bg_video, avatar_image, audio, script, voice, overlays, duration}
                'language' => 'en',
                'fps' => 30,
                'canvas' => ['w'=>1280,'h'=>720],
            ],
        ]);

        return view('studio.builder', [
            'project' => $project,
            'state'   => $project->state ?? [],
        ]);
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|integer|exists:projects,id',
            'title'      => 'required|string|max:120',
            'state'      => 'required|array',
        ]);

        $project = Project::findOrFail($data['project_id']);
        $project->update([
            'title' => $data['title'],
            'state' => $data['state'],
        ]);

        return response()->json(['ok' => true]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'type' => 'required|in:bg_video,avatar_image',
            'file' => 'required|file|max:102400', // 100MB
        ]);
        $folder = $request->type === 'bg_video' ? 'uploads/video' : 'uploads/avatars';
        $path = $request->file('file')->store($folder, 'public');

        return response()->json([
            'ok' => true,
            'path' => $path,
            'url' => asset('storage/'.$path),
        ]);
    }

    public function export(Request $request)
    {
        // Stub: here you would queue an FFmpeg job that:
        // 1) For each scene: (optional) run lipsync (e.g. Wav2Lip) with avatar_image + audio -> scene_video.mp4
        // 2) Composite bg_video + lipsync_video + overlays -> final scene mp4
        // 3) Concat all scenes -> final export
        // Return a fake URL for now.
        return response()->json([
            'ok' => true,
            'url' => asset('storage/videos/sample-output.mp4'),
            'message' => 'Export job queued (stub).'
        ]);
    }
}
