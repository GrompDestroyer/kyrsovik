<?php
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_error.log');
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Получаем сырые данные запроса
$raw_data = file_get_contents('php://input');
error_log("Raw request data: " . $raw_data);

// Декодируем JSON
$data = json_decode($raw_data, true);
error_log("Decoded data: " . print_r($data, true));

// Выводим данные для отладки
echo json_encode([
    'received_data' => $data,
    'request_method' => $_SERVER['REQUEST_METHOD'],
    'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'not set',
    'raw_data' => $raw_data
]);
?> 