<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\FileUpload;
use Illuminate\Support\Facades\Storage;

class FileManagerController extends Controller
{
    /**
     * Show the file manager index page.
     */
    public function index()
    {
        $audios = FileUpload::where('category', 'audio')->latest()->get();
        $videos = FileUpload::where('category', 'video')->latest()->get();
        $blenders = FileUpload::where('category', 'blender')->latest()->get();

        return view('file_manager.index', compact('audios', 'videos', 'blenders'));
    }

    /**
     * Store uploaded file in correct folder and log in DB.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:102400', // 100 MB max
            'category' => 'required|in:audio,video,blender',
        ]);

        $file = $request->file('file');
        $category = $request->category;

        // Save to storage/app/public/uploads/audio|video|blender
$path = $file->storeAs("uploads/{$category}", $file->getClientOriginalName(), 'public');

        FileUpload::create([
            'filename' => $path,
            'original_name' => $file->getClientOriginalName(),
            'category' => $category,
        ]);

        return redirect()->route('file_manager.index')->with('success', 'File uploaded successfully.');
    }

    /**
     * Delete a file.
     */
    public function destroy($id)
    {
        $file = FileUpload::findOrFail($id);

        // Delete from storage
        if (Storage::disk('public')->exists($file->filename)) {
            Storage::disk('public')->delete($file->filename);
        }

        $file->delete();

        return back()->with('success', 'File deleted.');
    }
    
    public function saveFromTTS(Request $request)
{
    $request->validate([
        'url' => 'required|url',
        'name' => 'nullable|string|max:100',
    ]);

    $url = $request->input('url');
    $name = $request->input('name') ?? 'tts_' . time();
    $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $name) . '.mp3';

    $audioData = file_get_contents($url);
    Storage::disk('public')->put('uploads/audio/' . $filename, $audioData);

    FileUpload::create([
        'filename' => 'uploads/audio/' . $filename,
        'original_name' => $filename,
        'category' => 'audio',
    ]);

    return response()->json(['success' => true]);
}

}
