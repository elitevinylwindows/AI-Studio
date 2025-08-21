<?php

namespace App\Http\Controllers;

use App\Models\Voice;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

// Do NOT import a specific TextToSpeechClient class here.
// We dynamically resolve the correct class inside buildTtsClient().

use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\SsmlVoiceGender;

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
        $vendors   = Voice::select('vendor')->distinct()->pluck('vendor');
        $languages = Voice::select('language_full')->distinct()->orderBy('language_full')->pluck('language_full');
        $genders   = Voice::select('gender')->distinct()->pluck('gender');
        $formats   = ['mp3', 'ogg', 'wav'];

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

    public function sync(Request $request)
    {
        [$client, $transport] = $this->buildTtsClient();
        $resp = $client->listVoices();

        $count = 0;

        foreach ($resp->getVoices() as $gVoice) {
            $name   = $gVoice->getName();                                // e.g. "en-US-Neural2-A"
            $gender = SsmlVoiceGender::name($gVoice->getSsmlGender());   // "MALE"/"FEMALE"/"NEUTRAL"
            $gender = $gender ? ucfirst(strtolower($gender)) : null;

            foreach ($gVoice->getLanguageCodes() as $code) {             // e.g. "en-US"
                $full = $this->languageFullFromCode($code);              // e.g. "English (United States)"

                // Upsert by (voice_id + language_code)
                Voice::updateOrCreate(
                    ['voice_id' => $name, 'language_code' => $code],
                    [
                        'vendor'        => 'Google',
                        'language'      => $code,
                        'language_full' => $full,
                        'voice_name'    => $name,
                        'gender'        => $gender,
                        'voice_engine'  => 'Neural',
                        // keep existing editable fields if set
                        'audio_format'  => DB::raw("COALESCE(audio_format, 'mp3')"),
                        'status'        => 'Active',
                    ]
                );
                $count++;
            }
        }

        return back()->with('success', "Synced $count voice entries via $transport.");
    }

    // ---------- Helpers ----------

    /**
     * Build a Google TTS client that:
     * - ALWAYS uses explicit credentials (no ADC),
     * - supports both namespaces (V1\* and V1\Client\*),
     * - forces REST transport.
     *
     * @return array{0:object,1:string} [$client, $transport]
     */
    private function buildTtsClient(): array
    {
        $transport = 'rest'; // avoid gRPC dependency issues

        // Load credentials as an ARRAY
        $creds = null;

        // A) Path from .env
        if ($path = env('GOOGLE_APPLICATION_CREDENTIALS')) {
            if (is_file($path) && is_readable($path)) {
                $json = file_get_contents($path);
                $creds = json_decode($json, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \RuntimeException('Invalid JSON in GOOGLE_APPLICATION_CREDENTIALS: '.json_last_error_msg());
                }
            }
        }

        // B) Fallback to storage
        if (!$creds) {
            $fallback = storage_path('app/keys/google-tts.json');
            if (is_file($fallback) && is_readable($fallback)) {
                $json = file_get_contents($fallback);
                $creds = json_decode($json, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \RuntimeException('Invalid JSON in fallback google-tts.json: '.json_last_error_msg());
                }
            }
        }

        // C) Inline JSON via .env (optional)
        if (!$creds && ($inline = env('GOOGLE_APPLICATION_CREDENTIALS_JSON'))) {
            $creds = json_decode($inline, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException('GOOGLE_APPLICATION_CREDENTIALS_JSON is not valid JSON: '.json_last_error_msg());
            }
        }

        if (!$creds) {
            // Stop instead of letting ADC run
            throw new \RuntimeException('Google TTS credentials not found. Set GOOGLE_APPLICATION_CREDENTIALS to an absolute readable file or GOOGLE_APPLICATION_CREDENTIALS_JSON to the JSON.');
        }

        // Pick whichever class exists
        $cls = '\Google\Cloud\TextToSpeech\V1\TextToSpeechClient';
        if (!class_exists($cls) && class_exists('\Google\Cloud\TextToSpeech\V1\Client\TextToSpeechClient')) {
            $cls = '\Google\Cloud\TextToSpeech\V1\Client\TextToSpeechClient';
        }
        if (!class_exists($cls)) {
            throw new \RuntimeException('TextToSpeechClient class not found. Install/update google/cloud-text-to-speech and run composer dump-autoload -o.');
        }

        $client = new $cls([
            'credentials' => $creds,
            'transport'   => $transport,
        ]);

        return [$client, $transport];
    }

    /**
     * Synthesize speech using current credentials / transport.
     *
     * @return array{0:string,1:string} [binary audio, file extension]
     */
    private function ttsSynthesize(string $text, string $languageCode, string $voiceName, string $format): array
    {
        [$client] = $this->buildTtsClient();

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

    /** Convert "en-US" → "English (United States)". Uses PHP intl when available; otherwise a small fallback map. */
    private function languageFullFromCode(string $bcp47): string
    {
        if (class_exists(\Locale::class)) {
            $norm = str_replace('_', '-', $bcp47);
            $dispLang = \Locale::getDisplayLanguage($norm, 'en'); // "English"
            $reg      = \Locale::getRegion($norm);                // "US" (may be "")
            $dispReg  = $reg ? \Locale::getDisplayRegion('und_'.$reg, 'en') : '';
            return $dispReg ? "{$dispLang} ({$dispReg})" : $dispLang;
        }

        static $fallback = [
            'en' => 'English', 'en-US' => 'English (United States)', 'en-GB' => 'English (United Kingdom)',
            'es' => 'Spanish', 'es-ES' => 'Spanish (Spain)', 'es-MX' => 'Spanish (Mexico)',
            'fr' => 'French',  'fr-FR' => 'French (France)', 'fr-CA' => 'French (Canada)',
            'de' => 'German',  'de-DE' => 'German (Germany)',
            'pt' => 'Portuguese', 'pt-BR' => 'Portuguese (Brazil)', 'pt-PT' => 'Portuguese (Portugal)',
            'it' => 'Italian', 'it-IT' => 'Italian (Italy)',
            'ja' => 'Japanese', 'ja-JP' => 'Japanese (Japan)',
            'ko' => 'Korean', 'ko-KR' => 'Korean (South Korea)',
            'zh' => 'Chinese', 'zh-CN' => 'Chinese (China)', 'zh-TW' => 'Chinese (Taiwan)', 'zh-HK' => 'Chinese (Hong Kong)',
        ];
        return $fallback[$bcp47] ?? $fallback[substr($bcp47, 0, 2)] ?? $bcp47;
    }
}
