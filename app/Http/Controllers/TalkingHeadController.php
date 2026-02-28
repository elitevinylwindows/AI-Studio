<?php

namespace App\Http\Controllers;

use App\Models\Avatar;
use App\Models\TalkingHead;
use App\Models\Voice;
use App\Services\TalkingHeadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Cloud\TextToSpeech\V1\Client\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SynthesizeSpeechRequest;

class TalkingHeadController extends Controller
{
    /* ────────────────────────────────────────────────
     *  INDEX — list all generated talking head videos
     * ──────────────────────────────────────────────── */
    public function index()
    {
        $videos = TalkingHead::where('user_id', Auth::id())
            ->with('avatar')
            ->latest()
            ->get();

        return view('talking_head.index', compact('videos'));
    }

    /* ────────────────────────────────────────────────
     *  CREATE — form: pick avatar, write script, choose voice
     * ──────────────────────────────────────────────── */
    public function create(Request $request)
    {
        $avatars = Avatar::ownedBy(Auth::id())
            ->where('status', 'active')
            ->latest()
            ->get();

        $languages = Voice::where('vendor', 'Google')
            ->whereNotNull('voice_text')
            ->select('language_full', 'language_code')
            ->distinct()
            ->orderBy('language_full')
            ->get();

        $selectedAvatar = $request->query('avatar_id');

        return view('talking_head.create', compact('avatars', 'languages', 'selectedAvatar'));
    }

    /* ────────────────────────────────────────────────
     *  STORE — generate TTS audio, save DB row, return JSON
     *  (fast — doesn't call Replicate yet)
     * ──────────────────────────────────────────────── */
    public function store(Request $request)
    {
        $request->validate([
            'avatar_id' => 'required|exists:avatars,id',
            'script'    => 'required|string|max:2000',
            'voice'     => 'required|string',
            'language'  => 'required|string',
            'title'     => 'nullable|string|max:255',
        ]);

        $avatar = Avatar::findOrFail($request->avatar_id);
        abort_unless($avatar->user_id === Auth::id(), 403);

        // Generate TTS audio
        $audioPath = $this->generateTTS(
            $request->script,
            $request->language,
            $request->voice
        );

        $talkingHead = TalkingHead::create([
            'user_id'    => Auth::id(),
            'avatar_id'  => $avatar->id,
            'title'      => $request->title ?: 'Video ' . now()->format('M d, H:i'),
            'script'     => $request->script,
            'voice_name' => $request->voice,
            'audio_path' => $audioPath,
            'status'     => 'pending',
        ]);

        return response()->json([
            'status' => 'ok',
            'id'     => $talkingHead->id,
            'message' => 'Audio generated. Starting video generation...',
        ]);
    }

    /* ────────────────────────────────────────────────
     *  GENERATE — AJAX: kick off Replicate prediction
     *  POST /talking-head/{id}/generate
     * ──────────────────────────────────────────────── */
    public function generate(TalkingHead $talkingHead)
    {
        abort_unless($talkingHead->user_id === Auth::id(), 403);

        set_time_limit(120);
        ini_set('max_execution_time', '120');

        $avatar = $talkingHead->avatar;

        // Build public URLs that Replicate can access
        $imageUrl = $avatar->image_url;
        $audioUrl = $talkingHead->audio_public_url;

        if (!$imageUrl || !$audioUrl) {
            return response()->json(['status' => 'error', 'error' => 'Missing image or audio URL.'], 422);
        }

        try {
            $service = new TalkingHeadService();
            $prediction = $service->startPrediction($imageUrl, $audioUrl);

            $talkingHead->update([
                'replicate_id' => $prediction['id'],
                'status'       => 'processing',
            ]);

            // If the prediction already completed (Prefer: wait header)
            if (($prediction['status'] ?? '') === 'succeeded' && !empty($prediction['output'])) {
                return $this->handleCompleted($talkingHead, $prediction);
            }

            return response()->json([
                'status'        => 'processing',
                'prediction_id' => $prediction['id'],
                'talking_head_id' => $talkingHead->id,
            ]);

        } catch (\Throwable $e) {
            Log::error('Talking head generation failed', [
                'id'    => $talkingHead->id,
                'error' => $e->getMessage(),
            ]);

            $talkingHead->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return response()->json(['status' => 'error', 'error' => $e->getMessage()], 422);
        }
    }

    /* ────────────────────────────────────────────────
     *  POLL — AJAX: check prediction status
     *  GET /talking-head/{id}/status
     * ──────────────────────────────────────────────── */
    public function status(TalkingHead $talkingHead)
    {
        abort_unless($talkingHead->user_id === Auth::id(), 403);

        // Already done?
        if (in_array($talkingHead->status, ['completed', 'failed'])) {
            return response()->json([
                'status'    => $talkingHead->status,
                'video_url' => $talkingHead->video_public_url,
                'error'     => $talkingHead->error_message,
            ]);
        }

        if (!$talkingHead->replicate_id) {
            return response()->json(['status' => 'pending']);
        }

        try {
            $service    = new TalkingHeadService();
            $prediction = $service->getPrediction($talkingHead->replicate_id);

            $replStatus = $prediction['status'] ?? 'unknown';

            if ($replStatus === 'succeeded' && !empty($prediction['output'])) {
                return $this->handleCompleted($talkingHead, $prediction);
            }

            if ($replStatus === 'failed') {
                $errMsg = $prediction['error'] ?? 'Replicate prediction failed.';
                $talkingHead->update([
                    'status'        => 'failed',
                    'error_message' => $errMsg,
                ]);
                return response()->json(['status' => 'failed', 'error' => $errMsg]);
            }

            // Still processing
            return response()->json(['status' => 'processing']);

        } catch (\Throwable $e) {
            return response()->json(['status' => 'processing', 'note' => $e->getMessage()]);
        }
    }

    /* ────────────────────────────────────────────────
     *  DESTROY
     * ──────────────────────────────────────────────── */
    public function destroy(TalkingHead $talkingHead)
    {
        abort_unless($talkingHead->user_id === Auth::id(), 403);

        // Delete local files
        if ($talkingHead->video_path) {
            $path = public_path('storage/' . $talkingHead->video_path);
            if (file_exists($path)) unlink($path);
        }
        if ($talkingHead->audio_path) {
            $path = public_path('storage/' . $talkingHead->audio_path);
            if (file_exists($path)) unlink($path);
        }

        $talkingHead->delete();

        return redirect()
            ->route('talking-head.index')
            ->with('success', 'Video deleted.');
    }

    /* ────────────────────────────────────────────────
     *  Private helpers
     * ──────────────────────────────────────────────── */

    /**
     * Handle a completed Replicate prediction — download video, save locally.
     */
    private function handleCompleted(TalkingHead $talkingHead, array $prediction)
    {
        $outputUrl = is_array($prediction['output'])
            ? ($prediction['output'][0] ?? $prediction['output'])
            : $prediction['output'];

        // Try to download the video locally
        $videoPath = null;
        try {
            $dir = public_path('storage/talking_heads');
            File::ensureDirectoryExists($dir);

            $filename  = 'th_' . $talkingHead->id . '_' . Str::random(8) . '.mp4';
            $content   = file_get_contents($outputUrl);

            if ($content) {
                file_put_contents($dir . '/' . $filename, $content);
                $videoPath = 'talking_heads/' . $filename;
            }
        } catch (\Throwable $e) {
            Log::warning('Could not download talking head video locally', ['error' => $e->getMessage()]);
        }

        $talkingHead->update([
            'status'    => 'completed',
            'video_url' => $outputUrl,
            'video_path' => $videoPath,
        ]);

        return response()->json([
            'status'    => 'completed',
            'video_url' => $talkingHead->video_public_url,
        ]);
    }

    /**
     * Generate TTS audio via Google Cloud, save to public/storage/tts/
     * Returns the relative path (e.g. 'tts/tts_abc123.mp3')
     */
    private function generateTTS(string $text, string $languageCode, string $voiceName): string
    {
        // Load credentials
        $creds = null;
        foreach ([
            env('GOOGLE_APPLICATION_CREDENTIALS'),
            base_path('storage/app/keys/google-tts.json'),
            base_path('storage/app/credentials/tts-key.json'),
        ] as $path) {
            if ($path && is_file($path) && is_readable($path)) {
                $creds = json_decode(file_get_contents($path), true);
                break;
            }
        }

        if (!$creds) {
            throw new \RuntimeException('Google TTS credentials not found.');
        }

        $scopes  = ['https://www.googleapis.com/auth/cloud-platform'];
        $saCreds = new ServiceAccountCredentials($scopes, $creds);

        $client = new TextToSpeechClient([
            'credentials'       => $saCreds,
            'credentialsConfig' => ['scopes' => $scopes],
            'transport'         => 'rest',
            'apiEndpoint'       => 'texttospeech.googleapis.com',
        ]);

        $synthReq = new SynthesizeSpeechRequest([
            'input'        => new SynthesisInput(['text' => $text]),
            'voice'        => new VoiceSelectionParams([
                'language_code' => $languageCode,
                'name'          => $voiceName,
            ]),
            'audio_config' => new AudioConfig(['audio_encoding' => AudioEncoding::MP3]),
        ]);

        $resp = $client->synthesizeSpeech($synthReq);

        $dir = public_path('storage/tts');
        File::ensureDirectoryExists($dir);

        $filename = 'tts_' . Auth::id() . '_' . Str::random(10) . '.mp3';
        file_put_contents($dir . '/' . $filename, $resp->getAudioContent());

        return 'tts/' . $filename;
    }
}
