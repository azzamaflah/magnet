<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$apiKey = config('services.gemini.api_key');

$client = new \GuzzleHttp\Client(['timeout' => 20]);

try {
    $response = $client->post(
        "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent?key={$apiKey}",
        [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'contents' => [
                    ['role' => 'user', 'parts' => [['text' => 'Halo, siapa kamu? Jawab singkat.']]]
                ],
                'generationConfig' => ['maxOutputTokens' => 100],
            ]
        ]
    );
    $data = json_decode($response->getBody(), true);
    echo "✅ SUCCESS!\n";
    echo "Reply: " . ($data['candidates'][0]['content']['parts'][0]['text'] ?? 'no reply') . "\n";
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    if (method_exists($e, 'getResponse') && $e->getResponse()) {
        echo "Body: " . substr($e->getResponse()->getBody(), 0, 500) . "\n";
    }
}
