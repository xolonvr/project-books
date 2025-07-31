<?php
// Подгружаем .env
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

header('Content-Type: application/json'); // <-- ВАЖНО!!!

$openai_api_key = $_ENV['OPENAI_API_KEY'] ?? '';
$google_api_key = $_ENV['GOOGLE_BOOKS_API_KEY'] ?? '';

$description = $_POST['description'] ?? '';
$genre = $_POST['genre'] ?? '';
$length = $_POST['length'] ?? '';
$country = $_POST['country'] ?? '';
$age = $_POST['age'] ?? '';

if (!$description) {
  echo json_encode(["error" => "Описание обязательно"]);
  exit;
}

$prompt = "Представь, что ты — библиотечный консультант, разбирающийся в сюжетах и сюжетных архетипах. Пользователь описал сюжет: '$description'. На основе этого:
1. Назови первую (самую релевантную) **реальную книгу**, в которой сюжет совпадает всерьез почти один в один.
2. Дай **ещё до 5** других книг (не обязательно на 100% совпадение, но близкие по духу и теме).";
if ($genre) $prompt .= " Жанр: $genre.";
if ($length) $prompt .= " Объём: $length.";
if ($country) $prompt .= " Страна: $country.";
if ($age) $prompt .= " Возрастное ограничение: $age.";
$prompt .= " Укажи только названия книг в виде списка без авторов.";

$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  "Authorization: Bearer $openai_api_key",
  "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
  "model" => "gpt-4",
  "messages" => [["role" => "user", "content" => $prompt]],
  "temperature" => 0.7
]));

$response = curl_exec($ch);
if (curl_errno($ch)) {
  echo json_encode(["error" => "Ошибка OpenAI: " . curl_error($ch)]);
  exit;
}
curl_close($ch);

$data = json_decode($response, true);
$titles_text = $data['choices'][0]['message']['content'] ?? '';
$titles = array_filter(array_map('trim', preg_split('/\n|\d+[.)-]? */', $titles_text)));

$results = [];

foreach ($titles as $title) {
  $query = urlencode($title);
  $url = "https://www.googleapis.com/books/v1/volumes?q=intitle:$query&key=$google_api_key&maxResults=1";

  $json = file_get_contents($url);
  if (!$json) continue;

  $bookData = json_decode($json, true);
  if (!isset($bookData['items'][0])) continue;

  $info = $bookData['items'][0]['volumeInfo'];

  $results[] = [
    'title' => $info['title'] ?? 'Без названия',
    'author' => implode(', ', $info['authors'] ?? ['Неизвестен']),
    'year' => substr($info['publishedDate'] ?? '0000', 0, 4),
    'description' => $info['description'] ?? 'Нет описания',
    'thumbnail' => $info['imageLinks']['thumbnail'] ?? ''
  ];
}

echo json_encode(["books" => $results]);
