<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Cloud\TextToSpeech\V1\Client\TextToSpeechClient;
use App\Models\Voice;

class VoiceController extends Controller
{
    public function index()
    {
        $voices = Voice::all();
        return view('voices.index', compact('voices'));
    }

    public function sync()
    {
        $client = new TextToSpeechClient();
        $response = $client->listVoices();

        foreach ($response->getVoices() as $voice) {
            foreach ($voice->getLanguageCodes() as $lang) {
                Voice::updateOrCreate(
                    ['voice_id' => $voice->getName()],
                    [
                        'vendor' => 'Google',
                        'language' => $lang,
                        'language_code' => $lang,
                        'voice_name' => $voice->getName(),
                        'voice_id' => $voice->getName(),
                        'gender' => $voice->getSsmlGender(),
                        'voice_engine' => $voice->getNaturalSampleRateHertz() ? 'Neural' : 'Standard',
                        'status' => 'Active',
                    ]
                );
            }
        }

        return redirect()->route('voices.index')->with('success', 'Voices synced from Google TTS.');
    }
}
