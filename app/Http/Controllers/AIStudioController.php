<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\FileUpload;

class AIStudioController extends Controller
{
    public function index(Request $request)
    {
        $videos = FileUpload::where('category', 'video')->get();
        $audios = FileUpload::where('category', 'audio')->get();
        $blenders = FileUpload::where('category', 'blender')->get();

        $selectedVideo = $videos->firstWhere('id', $request->query('bg_video'));
        $selectedAudio = $audios->firstWhere('id', $request->query('mp3_audio'));
        $selectedBlender = $blenders->firstWhere('id', $request->query('blender_id'));
        $selectedVoice = $request->query('voice');

        $videoPath = $selectedVideo ? asset('storage/' . $selectedVideo->filename) : null;

        return view('ai_studio.index', compact(
            'videos', 'audios', 'blenders',
            'selectedVideo', 'selectedAudio', 'selectedBlender', 'selectedVoice', 'videoPath'
        ));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'tts_text' => 'nullable|string|max:1000',
            'mp3_audio' => 'nullable|string',
            'blender_id' => 'nullable|string',
            'bg_video' => 'nullable|string',
            'voice' => 'nullable|string|max:50',
            'blender_file' => 'nullable|file|mimes:glb,fbx|max:10240',
            'mouth_mode' => 'required|in:auto,loop,none',
            'overlay_style' => 'required|in:webm,3d',
        ]);

        if (empty($request->tts_text) && empty($request->mp3_audio)) {
            return back()->withErrors(['tts_text' => 'Please provide a script or select an existing audio.'])->withInput();
        }

        // Save blender file if uploaded
        if ($request->hasFile('blender_file')) {
            $path = $request->file('blender_file')->store('uploads/blender', 'public');
            FileUpload::create([
                'filename' => $path,
                'original_name' => $request->file('blender_file')->getClientOriginalName(),
                'category' => 'blender',
            ]);
        }

        return redirect()->route('ai_studio.index', [
            'bg_video' => $request->bg_video,
            'mp3_audio' => $request->mp3_audio,
            'blender_id' => $request->blender_id,
            'voice' => $request->voice,
        ])->with('success', 'Studio settings loaded.')
            ->with('ttsText', $request->tts_text)
            ->with('mouthMode', $request->mouth_mode)
            ->with('overlayStyle', $request->overlay_style);
    }
}
