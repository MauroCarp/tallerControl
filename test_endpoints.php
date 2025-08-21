<?php
// Script para probar los endpoints de push notifications

$baseUrl = 'http://127.0.0.1:8000';

echo "🧪 Probando endpoints de Push Notifications...\n\n";

// Test 1: VAPID Public Key
echo "1. Probando /push/vapid-public-key\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/push/vapid-public-key');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   Status Code: $httpCode\n";
echo "   Response: " . substr($response, strpos($response, "\r\n\r\n") + 4) . "\n\n";

// Test 2: Get CSRF Token
echo "2. Obteniendo CSRF Token\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/test-api');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Extraer CSRF token
preg_match('/name="csrf-token" content="([^"]*)"/', $response, $matches);
$csrfToken = $matches[1] ?? '';

echo "   CSRF Token: " . ($csrfToken ? "✅ Obtenido" : "❌ No encontrado") . "\n\n";

// Test 3: Subscribe Endpoint
echo "3. Probando /push/subscribe\n";
$subscriptionData = json_encode([
    'endpoint' => 'https://test.endpoint.com/mock',
    'keys' => [
        'p256dh' => 'mock_p256dh_key',
        'auth' => 'mock_auth_token'
    ]
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/push/subscribe');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $subscriptionData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-CSRF-TOKEN: ' . $csrfToken
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   Status Code: $httpCode\n";
echo "   Response: " . substr($response, strpos($response, "\r\n\r\n") + 4) . "\n\n";

// Test 4: Send Test Notification
echo "4. Probando /push/send-test\n";
$notificationData = json_encode([
    'title' => 'Test Notification',
    'message' => 'This is a test message'
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/push/send-test');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $notificationData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-CSRF-TOKEN: ' . $csrfToken
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   Status Code: $httpCode\n";
echo "   Response: " . substr($response, strpos($response, "\r\n\r\n") + 4) . "\n\n";

// Cleanup
if (file_exists('cookies.txt')) {
    unlink('cookies.txt');
}

echo "✅ Pruebas completadas\n";
?>
