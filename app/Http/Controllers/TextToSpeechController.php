<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\FileUpload;
use App\Models\Voice;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Cloud\TextToSpeech\V1\Client\TextToSpeechClient; // new namespace variant
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SynthesizeSpeechRequest;

class TextToSpeechController extends Controller
{
    /** Show page with dynamic language & voice options from voices table */
    public function index(Request $request)
    {
        // Distinct languages, but only those that have a curated voice_text
        $languages = Voice::where('vendor', 'Google')
            ->whereNotNull('voice_text')
            ->select('language_full', 'language_code')
            ->distinct()
            ->orderBy('language_full')
            ->get();

        $selectedCode = $request->query('language') ?: optional($languages->first())->language_code;

        // Initial voices for selected language (only curated/with voice_text)
        $voices = collect();
        if ($selectedCode) {
            $voices = Voice::where('vendor', 'Google')
                ->whereNotNull('voice_text')
                ->where('language_code', $selectedCode)
                ->orderBy('voice_name')
                ->get(['voice_name','voice_text','audio_format','language_code','gender']);
        }

        return view('text_to_speech.index', compact('languages', 'voices', 'selectedCode'));
    }

    /** Dependent dropdown: return voices for a language code (JSON) */
    public function voicesByLanguage(Request $request)
    {
        $code = $request->query('code');
        $rows = Voice::where('vendor', 'Google')
            ->whereNotNull('voice_text')
            ->when($code, fn($q) => $q->where('language_code', $code))
            ->orderBy('voice_name')
            ->get(['voice_name','voice_text','audio_format','language_code','gender']);

        // normalize gender case
        foreach ($rows as $r) {
            $r->gender = $r->gender ? ucfirst(strtolower($r->gender)) : null;
        }
        return response()->json($rows);
    }

    /** Generate audio via Google TTS */
    public function generate(Request $request)
    {
        $request->validate([
            'text'     => 'required|string',
            'language' => 'required|string', // language_code (e.g., en-US)
            'voice'    => 'required|string', // voice_name (e.g., en-US-Neural2-A)
        ]);

        [$client] = $this->buildTtsClient();

        $input = new SynthesisInput(['text' => $request->input('text')]);
        $voice = new VoiceSelectionParams([
            'language_code' => $request->input('language'),
            'name'          => $request->input('voice'),
        ]);
        $audioConfig = new AudioConfig(['audio_encoding' => AudioEncoding::MP3]);

        $req = new SynthesizeSpeechRequest([
            'input'       => $input,
            'voice'       => $voice,
            'audio_config'=> $audioConfig,
        ]);

        $resp = $client->synthesizeSpeech($req);

        $fileName = ($request->input('audio_name') ?: 'tts-'.Str::random(8)).'.mp3';
        $filePath = 'public/tts/'.$fileName;

        Storage::put($filePath, $resp->getAudioContent());
        $url = Storage::url($filePath); // /storage/tts/...

        return response()->json(['url' => $url, 'filename' => $fileName]);
    }

    /** Optional: keep your file-manager saver */
    public function saveToFileManager(Request $request)
    {
        $request->validate([
            'url'  => 'required|url',
            'name' => 'nullable|string|max:255',
        ]);

        FileUpload::create([
            'name'        => $request->input('name') ?? basename($request->input('url')),
            'file_path'   => $request->input('url'),
            'category'    => 'audio',
            'uploaded_by' => auth()->id() ?? 1,
        ]);

        return response()->json(['success' => true]);
    }

    /** Build a robust Google TTS client (explicit creds, REST transport) */
    private function buildTtsClient(): array
    {
        $transport = 'rest';

        // 1) Load service-account JSON as array (try several locations)
        $creds = null;

        // A) .env absolute path
        if ($p = env('GOOGLE_APPLICATION_CREDENTIALS')) {
            if (is_file($p) && is_readable($p)) {
                $creds = json_decode(file_get_contents($p), true);
            }
        }
        // B) storage/app/keys/google-tts.json
        if (!$creds) {
            $p = base_path('storage/app/keys/google-tts.json');
            if (is_file($p) && is_readable($p)) {
                $creds = json_decode(file_get_contents($p), true);
            }
        }
        // C) storage/app/credentials/tts-key.json (your previous path)
        if (!$creds) {
            $p = base_path('storage/app/credentials/tts-key.json');
            if (is_file($p) && is_readable($p)) {
                $creds = json_decode(file_get_contents($p), true);
            }
        }
        // D) inline JSON env
        if (!$creds && ($inline = env('GOOGLE_APPLICATION_CREDENTIALS_JSON'))) {
            $creds = json_decode($inline, true);
        }
        // E) base64 JSON env
        if (!$creds && ($b64 = env('GOOGLE_APPLICATION_CREDENTIALS_B64'))) {
            $j = base64_decode($b64, true);
            $creds = $j ? json_decode($j, true) : null;
        }

        if (!$creds || json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Google TTS credentials not found or invalid JSON.');
        }
        foreach (['type','client_email','private_key'] as $k) {
            if (empty($creds[$k])) throw new \RuntimeException("Service-account JSON missing: {$k}");
        }

        // 2) Explicit OAuth creds (prevents UNAUTHENTICATED)
        $scopes  = ['https://www.googleapis.com/auth/cloud-platform'];
        $saCreds = new ServiceAccountCredentials($scopes, $creds);

        // 3) Construct client (supports new Client\ namespace you’re using)
        $client = new TextToSpeechClient([
            'credentials'       => $saCreds,
            'credentialsConfig' => ['scopes' => $scopes],
            'transport'         => $transport,
            'apiEndpoint'       => 'texttospeech.googleapis.com',
        ]);

        return [$client, $transport];
    }
}
