<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\FileUpload;
use Google\Cloud\TextToSpeech\V1\Client\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SynthesizeSpeechRequest;

class TextToSpeechController extends Controller
{
    public function index()
    {
        return view('text_to_speech.index');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'language' => 'required|string',
            'voice' => 'required|string',
        ]);

        // ✅ Set credentials path
        $client = new TextToSpeechClient([
            'credentials' => storage_path('app/credentials/tts-key.json'),
        ]);

        $inputText = new SynthesisInput([
            'text' => $request->input('text')
        ]);

        $voice = new VoiceSelectionParams([
            'language_code' => $request->input('language'),
            'name' => $request->input('voice'),
        ]);

        $audioConfig = new AudioConfig([
            'audio_encoding' => AudioEncoding::MP3
        ]);

        // ✅ Correct request object
        $synthesisRequest = new SynthesizeSpeechRequest([
            'input' => $inputText,
            'voice' => $voice,
            'audio_config' => $audioConfig,
        ]);

        $response = $client->synthesizeSpeech($synthesisRequest);

        $fileName = 'tts-' . Str::random(10) . '.mp3';
        $filePath = 'public/tts/' . $fileName;

        Storage::put($filePath, $response->getAudioContent());

        $url = Storage::url($filePath);

        return response()->json([
            'url' => $url,
            'filename' => $fileName
        ]);
    }

    public function saveToFileManager(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'name' => 'nullable|string|max:255',
        ]);

        $fileUrl = $request->input('url');
        $fileName = $request->input('name') ?? basename($fileUrl);

        FileUpload::create([
            'name' => $fileName,
            'file_path' => $fileUrl,
            'category' => 'audio',
            'uploaded_by' => auth()->id() ?? 1,
        ]);

        return response()->json(['success' => true]);
    }
}
