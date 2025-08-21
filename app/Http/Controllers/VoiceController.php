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
use Google\Cloud\TextToSpeech\V1\Client\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\ListVoicesRequest;
use Google\Cloud\TextToSpeech\V1\SynthesizeSpeechRequest;
use Google\Auth\Credentials\ServiceAccountCredentials;

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
    try {
        $text   = $voice->voice_text ?: 'Hello from Google Text to Speech';
        $format = $voice->audio_format ?: 'mp3';

        [$audioContent, $ext] = $this->ttsSynthesize($text, $voice->language_code, $voice->voice_name, $format);

        $dir = storage_path('app/public/tts_samples');
        if (!is_dir($dir)) mkdir($dir, 0775, true);

        $fileName = 'voice_'.$voice->id.'_'.Str::random(8).'.'.$ext;
        file_put_contents($dir.'/'.$fileName, $audioContent);

        // make sure you've run: php artisan storage:link
        $publicUrl = asset('storage/tts_samples/'.$fileName);
        $voice->update(['sample_url' => $publicUrl]);

        return response()->json(['url' => $publicUrl]);
    } catch (\Throwable $e) {
        return response()->json([
            'error' => get_class($e).': '.$e->getMessage()
        ], 422);
    }
}


    public function sync(Request $request)
    {
        [$client, $transport] = $this->buildTtsClient();

        $request = new ListVoicesRequest(); 
        $resp = $client->listVoices($request);

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
    $transport = 'rest';

    // ---- load the JSON as an array ----
    $creds = null;

    // A) .env absolute path
    if ($path = env('GOOGLE_APPLICATION_CREDENTIALS')) {
        if (is_file($path) && is_readable($path)) {
            $json  = file_get_contents($path);
            $creds = json_decode($json, true);
        }
    }

    // B) main storage fallback (recommended)
    if (!$creds) {
        $path = base_path('storage/app/keys/google-tts.json');
        if (is_file($path) && is_readable($path)) {
            $json  = file_get_contents($path);
            $creds = json_decode($json, true);
        }
    }

    // C) public/keys fallback (only if you must; this is a **filesystem path**, not a URL)
    if (!$creds) {
        $path = public_path('keys/google-tts.json');
        if (is_file($path) && is_readable($path)) {
            $json  = file_get_contents($path);
            $creds = json_decode($json, true);
        }
    }

    // D) inline JSON env
    if (!$creds && ($inline = env('GOOGLE_APPLICATION_CREDENTIALS_JSON'))) {
        $creds = json_decode($inline, true);
    }

    // E) base64 env
    if (!$creds && ($b64 = env('GOOGLE_APPLICATION_CREDENTIALS_B64'))) {
        $json  = base64_decode($b64, true);
        $creds = $json ? json_decode($json, true) : null;
    }

    if (!$creds || json_last_error() !== JSON_ERROR_NONE) {
        throw new \RuntimeException('Google TTS credentials not found or invalid JSON.');
    }
    foreach (['type','client_email','private_key'] as $k) {
        if (empty($creds[$k])) {
            throw new \RuntimeException("Service-account JSON missing: {$k}");
        }
    }

    // Build explicit OAuth creds (prevents UNAUTHENTICATED)
    $scopes  = ['https://www.googleapis.com/auth/cloud-platform'];
    $saCreds = new ServiceAccountCredentials($scopes, $creds);

    // pick the available client class
    $cls = '\Google\Cloud\TextToSpeech\V1\TextToSpeechClient';
    if (!class_exists($cls) && class_exists('\Google\Cloud\TextToSpeech\V1\Client\TextToSpeechClient')) {
        $cls = '\Google\Cloud\TextToSpeech\V1\Client\TextToSpeechClient';
    }
    if (!class_exists($cls)) {
        throw new \RuntimeException('TextToSpeechClient class not found.');
    }

    $client = new $cls([
        'credentials'       => $saCreds,
        'credentialsConfig' => ['scopes' => $scopes],
        'transport'         => $transport,
        'apiEndpoint'       => 'texttospeech.googleapis.com',
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

    // Pick encoding + extension
    switch ($format) {
        case 'ogg':
            $encoding = AudioEncoding::OGG_OPUS; $ext = 'ogg'; break;
        case 'wav':
            $encoding = AudioEncoding::LINEAR16; $ext = 'wav'; break;
        default:
            $encoding = AudioEncoding::MP3; $ext = 'mp3';
    }
    $audioConfig = (new AudioConfig())->setAudioEncoding($encoding);

    // NEW library (Client\…): needs a request object
    // OLD library: accepts 3 args
    if (class_exists('\Google\Cloud\TextToSpeech\V1\Client\TextToSpeechClient')) {
        $req = (new SynthesizeSpeechRequest())
            ->setInput($inputText)
            ->setVoice($voice)
            ->setAudioConfig($audioConfig);
        $response = $client->synthesizeSpeech($req);
    } else {
        $response = $client->synthesizeSpeech($inputText, $voice, $audioConfig);
    }

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
