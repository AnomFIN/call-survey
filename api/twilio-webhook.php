<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$authToken = getenv('TWILIO_AUTH_TOKEN') ?: '';
if ($authToken === '') {
    http_response_code(500);
    echo json_encode(['error' => 'Twilio auth token missing']);
    exit;
}

$twilioSignature = $_SERVER['HTTP_X_TWILIO_SIGNATURE'] ?? '';
if ($twilioSignature === '') {
    http_response_code(403);
    echo json_encode(['error' => 'Missing Twilio signature']);
    exit;
}

$requestUrl = (function (): string {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $uri = $_SERVER['REQUEST_URI'] ?? '/api/twilio-webhook.php';
    return sprintf('%s://%s%s', $scheme, $host, $uri);
})();

$params = $_POST;
if (empty($params) && ($input = file_get_contents('php://input'))) {
    $decoded = json_decode($input, true);
    if (is_array($decoded)) {
        $params = $decoded;
    }
}

ksort($params);

$payload = $requestUrl;
foreach ($params as $key => $value) {
    $payload .= $key . (is_scalar($value) ? (string) $value : json_encode($value, JSON_UNESCAPED_UNICODE));
}

$computed = base64_encode(hash_hmac('sha1', $payload, $authToken, true));

if (!hash_equals($computed, $twilioSignature)) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid Twilio signature']);
    exit;
}

echo json_encode([
    'status' => 'ok',
    'message' => 'Twilio webhook validated',
    'timestamp' => gmdate('c'),
]);
