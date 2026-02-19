<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiService
{
    public function __construct(
        protected string $apiKey,
        protected string $model
    ) {}

    public static function fromConfig(): self
    {
        $key = config('services.gemini.api_key', '');
        $model = config('services.gemini.model', 'gemini-2.5-flash-preview-09-2025');
        return new self($key, $model);
    }

    /**
     * @throws \InvalidArgumentException when API key is empty
     * @throws \RuntimeException on API or network error
     */
    public function analyze(string $prompt): string
    {
        if ($this->apiKey === '') {
            throw new \InvalidArgumentException('Chưa cấu hình API key.');
        }

        $url = sprintf(
            'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
            $this->model,
            $this->apiKey
        );

        $response = Http::timeout(30)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException(
                'Gemini API error: ' . ($response->json('error.message') ?? $response->body() ?: $response->reason())
            );
        }

        $body = $response->json();
        $text = $body['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if ($text === null) {
            throw new \RuntimeException('Gemini API returned no text.');
        }

        return $text;
    }
}
