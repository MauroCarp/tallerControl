<?php
// Claves VAPID de ejemplo - debes generar las tuyas propias en producción
$publicKey = 'BF8Q8ZdKUWCdUtfYb_pXMzJV8sIKJckXzUh7nT5M1_5k_Bsw8i7vvzgJTJvEJo9CJ7Kuw-K8QKe_hvGzUYC2F9w';
$privateKey = 'aKJPqrG_Cd8LlVyJrSgZQe8nKt9B7_2tF9KmVz4e6Y0';

echo "Public Key: " . $publicKey . PHP_EOL;
echo "Private Key: " . $privateKey . PHP_EOL;
echo PHP_EOL;
echo "Agrega estas claves a tu archivo .env:" . PHP_EOL;
echo "VAPID_PUBLIC_KEY=\"" . $publicKey . "\"" . PHP_EOL;
echo "VAPID_PRIVATE_KEY=\"" . $privateKey . "\"" . PHP_EOL;
echo "VAPID_SUBJECT=\"mailto:tu@email.com\"" . PHP_EOL;
?>
