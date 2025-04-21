<?php
// Настраиваем логирование
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_error.log');
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Устанавливаем кодировку
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

// Устанавливаем заголовки CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Функция для отправки JSON-ответа
function sendJsonResponse($success, $message, $code = 200, $data = []) {
    http_response_code($code);
    $response = ['success' => $success, 'message' => $message];
    if (!empty($data)) {
        $response = array_merge($response, $data);
    }
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// Обработка OPTIONS запроса
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    sendJsonResponse(true, 'OK', 200);
}

require_once 'db.php';
session_start();

// Проверяем метод запроса
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, 'Неверный метод запроса', 405);
}

try {
    // Получаем JSON данные
    $json = file_get_contents('php://input');
    error_log("Received raw input: " . $json);
    
    if (empty($json)) {
        error_log("Empty request body");
        sendJsonResponse(false, 'Пустой запрос', 400);
    }

    $data = json_decode($json, true);
    
    if ($data === null) {
        error_log("JSON decode error: " . json_last_error_msg());
        sendJsonResponse(false, 'Некорректный формат данных', 400);
    }

    $username = isset($data['username']) ? trim($data['username']) : '';
    $password = isset($data['password']) ? $data['password'] : '';

    // Проверка на пустые поля
    if (empty($username) || empty($password)) {
        error_log("Empty fields: username=[" . $username . "], password=" . (empty($password) ? "empty" : "not empty"));
        sendJsonResponse(false, 'Все поля должны быть заполнены', 400);
    }

    // Поиск пользователя
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Успешная авторизация
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        
        error_log("Successful login attempt for user: " . $username);
        
        sendJsonResponse(true, 'Авторизация успешна', 200, [
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email']
            ]
        ]);
    } else {
        error_log("Failed login attempt for user: " . $username);
        sendJsonResponse(false, 'Неверное имя пользователя или пароль', 401);
    }
} catch(PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    sendJsonResponse(false, 'Ошибка сервера при авторизации', 500);
} catch(Exception $e) {
    error_log("General error: " . $e->getMessage());
    sendJsonResponse(false, 'Внутренняя ошибка сервера', 500);
}
?>