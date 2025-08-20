<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CreateVideoController extends Controller
{
    /**
     * Show the create video modal page.
     * You can embed the modal in here or include it via @include().
     */
    public function index()
    {
        return view('create_videos.create');
    }

    /**
     * Handle starting a new video project.
     * $orientation can be 'portrait' or 'landscape'.
     */
    public function start(Request $request)
    {
        $orientation = $request->get('orientation', 'landscape'); // default landscape

        // TODO: You can initialize a video project in DB here.
        // Example:
        // $video = VideoProject::create([
        //     'orientation' => $orientation,
        //     'status' => 'draft',
        //     'user_id' => auth()->id(),
        // ]);

        return view('create_videos.editor', compact('orientation'));
    }

    public function translate()
    {
        return view('create_videos.translate');
    }

    public function templates()
    {
        return view('create_videos.templates');
    }

    public function ppt()
    {
        return view('create_videos.ppt');
    }

    public function script()
    {
        return view('create_videos.script');
    }

    public function pdf2video()
    {
        return view('create_videos.pdf2video');
    }
}
