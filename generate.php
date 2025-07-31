<?php
require_once __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use Dotenv\Dotenv;

header('Content-Type: application/json');

// Загружаем ключ из .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$apiKey = $_ENV['OPENAI_API_KEY'] ?? null;
if (!$apiKey) {
    http_response_code(500);
    echo json_encode(['error' => 'API ключ не найден.']);
    exit;
}

// Получаем промт
$input = json_decode(file_get_contents('php://input'), true);
$prompt = $input['storyPrompt'] ?? '';

if (!$prompt) {
    http_response_code(400);
    echo json_encode(['error' => 'Промт не передан.']);
    exit;
}

// Отправляем в OpenAI
$client = new Client();

try {
    $response = $client->post('https://api.openai.com/v1/chat/completions', [
        'headers' => [
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json',
        ],
        'json' => [
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'system', 'content' => 'Ты талантливый писатель, твоя задача — генерировать интересные и связные художественные истории по описанию.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.8,
            'max_tokens' => 800,
        ]
    ]);

    $body = json_decode($response->getBody(), true);
    $story = $body['choices'][0]['message']['content'] ?? 'История не сгенерирована.';
    echo json_encode(['story' => $story]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка генерации: ' . $e->getMessage()]);
}