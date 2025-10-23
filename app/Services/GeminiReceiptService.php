<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Str;

class GeminiReceiptService
{
    public function __construct(
        private ?string $apiKey = null,
        private ?string $model = null,
        private ?Client $http = null,
    ) {
        $this->apiKey = $this->apiKey ?? config('services.gemini.key', env('GEMINI_API_KEY'));
        $this->model  = $this->model  ?? config('services.gemini.model', env('GEMINI_MODEL', 'gemini-1.5-flash'));
        $this->http   = $this->http   ?? new Client([
            'base_uri'        => 'https://generativelanguage.googleapis.com/v1beta/',
            'timeout'         => 30,
            'connect_timeout' => 10,
        ]);
    }

    public function extract(string $absoluteImagePath): array
    {
        $imageBytes = base64_encode(file_get_contents($absoluteImagePath));

        $prompt = [
            "role" => "user",
            "parts" => [
                ["text" => "Kamu adalah extractor struk belanja. Kembalikan HANYA JSON valid ..."],
                ["inline_data" => ["mime_type" => "image/jpeg", "data" => $imageBytes]],
            ],
        ];

        try {
            $response = $this->postWithRetry(
                sprintf('models/%s:generateContent', $this->model),
                [
                    'query' => ['key' => $this->apiKey],
                    'json'  => ['contents' => [$prompt]],
                ],
                2
            );
        } catch (ConnectException $e) {
            throw new \RuntimeException('Tidak bisa terhubung ke layanan AI. Coba lagi beberapa saat.');
        } catch (RequestException $e) {
            $errno = $e->getHandlerContext()['errno'] ?? null;
            if ($errno === 28) {
                throw new \RuntimeException('Proses AI timeout. Silakan coba lagi atau isi manual.');
            }
            throw new \RuntimeException('Permintaan AI gagal: ' . ($e->getMessage() ?: 'unknown'));
        }

        $data = json_decode($response->getBody()->getContents(), true);
        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
        $text = Str::of(trim($text))->replace(['```json', '```'], '')->trim();
        $json = json_decode((string) $text, true);

        if (!is_array($json)) {
            return ['merchant' => null, 'datetime' => null, 'total' => null, 'currency' => null, 'items' => []];
        }

        $json['items'] = array_values(array_map(function ($it) {
            return [
                'name'       => $it['name']       ?? 'Item',
                'qty'        => (float)($it['qty'] ?? 1),
                'unit_price' => isset($it['unit_price']) ? (float)$it['unit_price'] : null,
                'subtotal'   => isset($it['subtotal'])   ? (float)$it['subtotal']   : null,
            ];
        }, $json['items'] ?? []));

        $json['total']    = isset($json['total']) ? (float)$json['total'] : null;
        $json['merchant'] = $json['merchant'] ?? null;
        $json['currency'] = $json['currency'] ?? null;

        return $json;
    }

    private function postWithRetry(string $uri, array $options, int $maxRetry = 2)
    {
        $attempt = 0;
        beginning:
        try {
            return $this->http->post($uri, $options);
        } catch (ConnectException | RequestException $e) {
            if ($attempt++ < $maxRetry) {
                usleep((int) (pow(2, $attempt) * 400_000)); // 0.8s, 1.6s
                goto beginning;
            }
            throw $e;
        }
    }
}