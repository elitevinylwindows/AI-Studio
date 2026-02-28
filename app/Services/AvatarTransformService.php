<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class AvatarTransformService
{
    /**
     * Transform an uploaded photo into the requested avatar style using OpenAI.
     *
     * @param  string $sourceImagePath  Absolute path to the source image file
     * @param  string $style            'realistic' | 'cartoon' | '3d'
     * @return string|null              Binary PNG content of the transformed image, or null on failure
     */
    public function transform(string $sourceImagePath, string $style): ?string
    {
        if ($style === 'realistic') {
            return file_get_contents($sourceImagePath);
        }

        $apiKey = env('OPENAI_API_KEY');
        if (!$apiKey) {
            throw new \RuntimeException('OPENAI_API_KEY is not set in .env');
        }

        $prompt = $this->buildPrompt($style);

        // Use cURL directly for reliable multipart/form-data upload to OpenAI
        $ch = curl_init();

        $postFields = [
            'image'  => new \CURLFile($sourceImagePath, 'image/png', 'avatar.png'),
            'prompt' => $prompt,
            'model'  => 'gpt-image-1',
            'n'      => 1,
            'size'   => '1024x1024',
        ];

        curl_setopt_array($ch, [
            CURLOPT_URL            => 'https://api.openai.com/v1/images/edits',
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $postFields,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $apiKey,
            ],
        ]);

        $responseBody = curl_exec($ch);
        $httpCode     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError    = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            Log::error('OpenAI image transform cURL error', ['error' => $curlError]);
            throw new \RuntimeException('cURL error: ' . $curlError);
        }

        if ($httpCode !== 200) {
            Log::error('OpenAI image transform failed', [
                'http_code' => $httpCode,
                'body'      => $responseBody,
            ]);

            // Try to extract a human-readable error
            $decoded = json_decode($responseBody, true);
            $msg = $decoded['error']['message'] ?? "HTTP {$httpCode}";
            throw new \RuntimeException('OpenAI API error: ' . $msg);
        }

        $data = json_decode($responseBody, true);

        // Response contains base64-encoded image data
        if (!empty($data['data'][0]['b64_json'])) {
            return base64_decode($data['data'][0]['b64_json']);
        }

        // Or a URL to the image
        if (!empty($data['data'][0]['url'])) {
            $content = file_get_contents($data['data'][0]['url']);
            return $content ?: null;
        }

        Log::error('OpenAI image transform: unexpected response', ['data' => $data]);
        return null;
    }

    /**
     * Build the transformation prompt based on the desired style.
     */
    private function buildPrompt(string $style): string
    {
        switch ($style) {
            case 'cartoon':
                return 'Transform this photo into a high-quality cartoon/illustrated avatar style. '
                     . 'Keep the person\'s likeness, facial features, hair style, and clothing recognizable. '
                     . 'Use clean lines, vibrant colors, and a modern illustration style similar to '
                     . 'professional avatar illustrations used in tech companies. '
                     . 'The background should be clean and simple. '
                     . 'Make it look polished and professional, suitable for a business avatar.';

            case '3d':
                return 'Transform this photo into a high-quality 3D rendered avatar character. '
                     . 'Keep the person\'s likeness, facial features, hair style, and clothing recognizable. '
                     . 'Use a modern 3D render style similar to Pixar or Disney character design. '
                     . 'Smooth skin, soft lighting, subtle shadows, slightly stylized proportions. '
                     . 'The background should be clean and simple. '
                     . 'Make it look polished and professional, suitable for a 3D avatar in a video.';

            default:
                return 'Keep this image as-is with minor enhancement for use as a professional avatar.';
        }
    }
}
