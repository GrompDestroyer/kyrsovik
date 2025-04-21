<?php
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_error.log');
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Получаем сырые данные запроса
$raw_data = file_get_contents('php://input');
error_log("Raw login request data: " . $raw_data);

// Декодируем JSON
$data = json_decode($raw_data, true);
error_log("Decoded login data: " . print_r($data, true));

require_once 'db.php';

try {
    // Проверяем существование пользователя
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$data['username']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'received_data' => $data,
        'request_method' => $_SERVER['REQUEST_METHOD'],
        'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'not set',
        'raw_data' => $raw_data,
        'user_exists' => !empty($user),
        'password_match' => !empty($user) ? password_verify($data['password'], $user['password']) : false,
        'user_data' => $user ? array_merge($user, ['password' => '[HIDDEN]']) : null
    ]);
} catch(Exception $e) {
    echo json_encode([
        'error' => $e->getMessage(),
        'received_data' => $data
    ]);
}
?> 