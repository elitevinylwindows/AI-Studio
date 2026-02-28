<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
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
            // No transformation needed — return original file content
            return file_get_contents($sourceImagePath);
        }

        $apiKey = env('OPENAI_API_KEY');
        if (!$apiKey) {
            throw new \RuntimeException('OPENAI_API_KEY is not set in .env');
        }

        $prompt = $this->buildPrompt($style);

        // Use OpenAI's gpt-image-1 model (image editing / generation)
        // We send the original image + a style prompt to get a transformed version
        $response = Http::timeout(120)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])
            ->attach('image', file_get_contents($sourceImagePath), 'avatar.png')
            ->post('https://api.openai.com/v1/images/edits', [
                ['name' => 'prompt',  'contents' => $prompt],
                ['name' => 'model',   'contents' => 'gpt-image-1'],
                ['name' => 'n',       'contents' => '1'],
                ['name' => 'size',    'contents' => '1024x1024'],
            ]);

        if (!$response->successful()) {
            Log::error('OpenAI image transform failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return null;
        }

        $data = $response->json();

        // The response contains base64-encoded image data
        if (!empty($data['data'][0]['b64_json'])) {
            return base64_decode($data['data'][0]['b64_json']);
        }

        // Or it might return a URL
        if (!empty($data['data'][0]['url'])) {
            return file_get_contents($data['data'][0]['url']);
        }

        Log::error('OpenAI image transform: unexpected response format', ['data' => $data]);
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
