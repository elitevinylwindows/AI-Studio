<?php

namespace App\Http\Controllers;

use App\Models\Voice;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

// If you already created a TTS factory/service, you can reuse it.
// For a self-contained example, we'll inline a tiny client maker:
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Google\Cloud\TextToSpeech\V1\AudioConfig;

class VoiceController extends Controller
{
    public function index(Request $request)
    {
        // Filters (optional)
        $vendor   = $request->get('vendor');
        $langFull = $request->get('language_full');
        $code     = $request->get('language_code');
        $gender   = $request->get('gender');
        $format   = $request->get('audio_format');

        $q = Voice::query();

        if ($vendor)   $q->where('vendor', $vendor);
        if ($langFull) $q->where('language_full', $langFull);
        if ($code)     $q->where('language_code', $code);
        if ($gender)   $q->where('gender', $gender);
        if ($format)   $q->where('audio_format', $format);

        $voices = $q->orderBy('language_full')->orderBy('voice_name')->get();

        // Distinct filter values
        $vendors      = Voice::select('vendor')->distinct()->pluck('vendor');
        $languages    = Voice::select('language_full')->distinct()->orderBy('language_full')->pluck('language_full');
        $genders      = Voice::select('gender')->distinct()->pluck('gender');
        $formats      = ['mp3', 'ogg', 'wav'];

        return view('voices.index', compact('voices', 'vendors', 'languages', 'genders', 'formats'));
    }

    // dependent dropdown: codes by language_full
    public function codes(Request $request)
    {
        $request->validate(['language_full' => 'required|string']);
        $codes = Voice::where('language_full', $request->language_full)
            ->select('language_code')->distinct()->orderBy('language_code')->pluck('language_code');

        return response()->json($codes);
    }

    // Save only voice_text and audio_format
    public function update(Request $request, Voice $voice)
    {
        $request->validate([
            'voice_text'   => 'nullable|string|max:1000',
            'audio_format' => 'required|in:mp3,ogg,wav',
        ]);

        $voice->update([
            'voice_text'   => $request->voice_text,
            'audio_format' => $request->audio_format,
        ]);

        return response()->json(['status' => 'ok', 'message' => 'Updated']);
    }

    // Generate preview (saves a sample file and returns its URL)
    public function preview(Request $request, Voice $voice)
    {
        $text   = $voice->voice_text ?: 'Hello from Google Text to Speech';
        $format = $voice->audio_format ?: 'mp3';

        [$audioContent, $ext] = $this->ttsSynthesize($text, $voice->language_code, $voice->voice_name, $format);

        // Store file
        $dir = storage_path('app/public/tts_samples');
        if (!is_dir($dir)) mkdir($dir, 0775, true);

        $fileName = 'voice_'.$voice->id.'_'.Str::random(8).'.'.$ext;
        file_put_contents($dir.'/'.$fileName, $audioContent);

        // update sample_url for convenience
        $publicUrl = asset('storage/tts_samples/'.$fileName);
        $voice->update(['sample_url' => $publicUrl]);

        return response()->json(['url' => $publicUrl]);
    }

    // --- helpers ---

    private function ttsClient(): TextToSpeechClient
    {
        // Prefer path from env; fallback to storage; force REST to avoid gRPC requirement
        $path = env('GOOGLE_APPLICATION_CREDENTIALS');
        if ($path && is_file($path)) {
            $creds = json_decode(file_get_contents($path), true);
            return new TextToSpeechClient(['credentials' => $creds, 'transport' => 'rest']);
        }
        $fallback = storage_path('app/keys/google-tts.json');
        if (is_file($fallback)) {
            $creds = json_decode(file_get_contents($fallback), true);
            return new TextToSpeechClient(['credentials' => $creds, 'transport' => 'rest']);
        }
        // If you set GOOGLE_APPLICATION_CREDENTIALS_JSON with the raw JSON:
        if ($json = env('GOOGLE_APPLICATION_CREDENTIALS_JSON')) {
            $creds = json_decode($json, true);
            return new TextToSpeechClient(['credentials' => $creds, 'transport' => 'rest']);
        }

        // As a last resort, try ADC (may fail if not configured)
        return new TextToSpeechClient(['transport' => 'rest']);
    }

    /**
     * @return array{0:string,1:string} [binary audio, file extension]
     */
    private function ttsSynthesize(string $text, string $languageCode, string $voiceName, string $format): array
    {
        $client = $this->ttsClient();

        $inputText = (new SynthesisInput())->setText($text);

        $voice = (new VoiceSelectionParams())
            ->setLanguageCode($languageCode) // e.g. "en-US"
            ->setName($voiceName);           // e.g. "en-US-Neural2-A"

        // Pick encoding
        switch ($format) {
            case 'ogg':
                $encoding = AudioEncoding::OGG_OPUS; $ext = 'ogg'; break;
            case 'wav':
                $encoding = AudioEncoding::LINEAR16; $ext = 'wav'; break;
            default:
                $encoding = AudioEncoding::MP3; $ext = 'mp3';
        }

        $audioConfig = (new AudioConfig())->setAudioEncoding($encoding);

        $response = $client->synthesizeSpeech($inputText, $voice, $audioConfig);
        $audioContent = $response->getAudioContent();

        return [$audioContent, $ext];
    }
}
