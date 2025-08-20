<?php

namespace App\Http\Controllers;

use App\Models\VoiceTitle; // <-- add this
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Google\Cloud\TextToSpeech\V1\Client\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\ListVoicesRequest;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;

class VoiceController extends Controller
{
    private function initGoogleTTS()
    {
        $keyPath = storage_path('app/google/tts-key.json');

        if (!File::exists($keyPath)) {
            throw new \Exception("TTS key file not found at: {$keyPath}");
        }

        putenv("GOOGLE_APPLICATION_CREDENTIALS={$keyPath}");

        return new TextToSpeechClient();
    }

    public function index()
    {
        $client = $this->initGoogleTTS();
        $request = new ListVoicesRequest();
        $response = $client->listVoices($request);

        // Load any saved custom titles in one query (voice_name => display_title)
        $customMap = VoiceTitle::pluck('display_title', 'voice_name')->toArray();

        $voices = [];
        foreach ($response->getVoices() as $voice) {
            $name = $voice->getName();
            $langCodes = iterator_to_array($voice->getLanguageCodes());
            $languageCode = $langCodes[0] ?? 'en-US';

            $voices[] = [
                'name'           => $name,
                'languageCodes'  => $langCodes,
                'gender'         => $voice->getSsmlGender(), // 0..3
                'rate'           => $voice->getNaturalSampleRateHertz(),
                'display_title'  => $customMap[$name] ?? $name, // <-- inject saved title
                'language_code'  => $languageCode,
            ];
        }

        $client->close();

        return view('voices.index', compact('voices'));
    }

    public function preview(Request $request)
    {
        $request->validate([
            'voice' => 'required|string',
        ]);

        $client = $this->initGoogleTTS();

        $inputText = new SynthesisInput();
        $inputText->setText("Hello! This is a preview of my voice.");

        $voice = new VoiceSelectionParams();
        $voice->setName($request->voice);
        $voice->setLanguageCode(substr($request->voice, 0, 5)); // e.g., en-US

        $audioConfig = new AudioConfig();
        $audioConfig->setAudioEncoding(AudioEncoding::MP3);

        $response = $client->synthesizeSpeech($inputText, $voice, $audioConfig);

        $filename = 'preview_' . time() . '.mp3';
        $path = 'tts/' . $filename;

        Storage::disk('public')->put($path, $response->getAudioContent());

        $client->close();

        return response()->json([
            'url' => asset('storage/' . $path)
        ]);
    }

    // NEW: rename endpoint
    public function rename(Request $request)
    {
        $data = $request->validate([
            'voice' => 'required|string',         // Google's voice name (unique)
            'title' => 'required|string|max:120', // your custom display title
        ]);

        // Try to guess language_code from voice string, fallback null
        $languageCode = substr($data['voice'], 0, 5);
        if (!preg_match('/^[a-z]{2}-[A-Z]{2}$/', $languageCode)) {
            $languageCode = null;
        }

        $record = VoiceTitle::updateOrCreate(
            ['voice_name' => $data['voice']],
            ['display_title' => $data['title'], 'language_code' => $languageCode]
        );

        return response()->json([
            'ok'    => true,
            'title' => $record->display_title,
        ]);
    }
}
