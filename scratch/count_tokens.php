<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$apiKey = config('services.gemini.api_key');
$client = new \GuzzleHttp\Client(['timeout' => 20]);

$reflection = new ReflectionClass(\App\Http\Controllers\ChatbotController::class);
$prop = $reflection->getProperty('systemPrompt');
$prop->setAccessible(true);
$controller = new \App\Http\Controllers\ChatbotController();
$systemPrompt = $prop->getValue($controller);

try {
    $resp = $client->post(
        "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:countTokens?key={$apiKey}",
        [
            'json' => [
                'contents' => [
                    ['role' => 'user', 'parts' => [['text' => $systemPrompt . "\n\nUser: Bagaimana cara mendaftar magang di BPS Bantul?"]]]
                ]
            ]
        ]
    );
    echo "Prompt Tokens: " . $resp->getBody() . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    if (method_exists($e, 'getResponse') && $e->getResponse()) {
        echo "Body: " . $e->getResponse()->getBody() . "\n";
    }
}
