<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class TalkingHeadService
{
    private string $apiToken;
    private string $modelVersion = 'cjwbw/sadtalker';

    public function __construct()
    {
        $this->apiToken = env('REPLICATE_API_TOKEN', '');
        if (!$this->apiToken) {
            throw new \RuntimeException('REPLICATE_API_TOKEN is not set in .env');
        }
    }

    /**
     * Start a talking head prediction on Replicate.
     * Returns the prediction ID for polling.
     *
     * @param  string $imageUrl   Public URL to the avatar image
     * @param  string $audioUrl   Public URL to the TTS audio file
     * @param  string $preprocess  'crop' | 'resize' | 'full'
     * @return array  {id, status, ...}
     */
    public function startPrediction(string $imageUrl, string $audioUrl, string $preprocess = 'full'): array
    {
        $payload = [
            'version' => $this->getLatestVersion(),
            'input'   => [
                'source_image' => $imageUrl,
                'driven_audio' => $audioUrl,
                'preprocess'   => $preprocess,
                'enhancer'     => 'gfpgan',
            ],
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => 'https://api.replicate.com/v1/predictions',
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->apiToken,
                'Content-Type: application/json',
                'Prefer: wait',  // wait up to 60s for result
            ],
        ]);

        $body     = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err      = curl_error($ch);
        curl_close($ch);

        if ($err) {
            throw new \RuntimeException('Replicate cURL error: ' . $err);
        }

        $data = json_decode($body, true);

        if ($httpCode >= 400) {
            $msg = $data['detail'] ?? $data['error'] ?? "HTTP {$httpCode}";
            Log::error('Replicate prediction start failed', ['http' => $httpCode, 'body' => $body]);
            throw new \RuntimeException('Replicate error: ' . $msg);
        }

        return $data;
    }

    /**
     * Poll a prediction's status.
     *
     * @param  string $predictionId
     * @return array  {id, status, output, error, ...}
     */
    public function getPrediction(string $predictionId): array
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => "https://api.replicate.com/v1/predictions/{$predictionId}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->apiToken,
                'Content-Type: application/json',
            ],
        ]);

        $body     = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($body, true);

        if ($httpCode >= 400) {
            $msg = $data['detail'] ?? "HTTP {$httpCode}";
            throw new \RuntimeException('Replicate poll error: ' . $msg);
        }

        return $data;
    }

    /**
     * Get the latest model version hash for SadTalker.
     */
    private function getLatestVersion(): string
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => "https://api.replicate.com/v1/models/{$this->modelVersion}/versions",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->apiToken,
                'Content-Type: application/json',
            ],
        ]);

        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code === 200) {
            $data = json_decode($body, true);
            if (!empty($data['results'][0]['id'])) {
                return $data['results'][0]['id'];
            }
        }

        // Fallback: known working version
        return 'a519cc0cfebaaeade068b23899165a11ec76aaa1a2efc5cf30e6c784c1577bea';
    }
}
