<?php
// Настраиваем логирование
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_error.log');
error_reporting(E_ALL);
ini_set('display_errors', 1); // Включаем отображение ошибок для отладки

// Устанавливаем кодировку
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

// Устанавливаем заголовки CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Создаем папку для логов, если её нет
if (!file_exists(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0777, true);
}

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

// Проверяем, что запрос пришел методом POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("Error: Invalid request method - " . $_SERVER['REQUEST_METHOD']);
    sendJsonResponse(false, 'Неверный метод запроса', 405);
}

try {
    // Получаем JSON данные из тела запроса
    $json = file_get_contents('php://input');
    file_put_contents(__DIR__ . '/logs/last_request.log', $json); // Сохраняем последний запрос
    error_log("Step 1 - Raw JSON data received: " . $json);

    $data = json_decode($json, true);
    error_log("Step 2 - Decoded JSON data: " . print_r($data, true));

    // Проверяем корректность JSON данных
    if ($data === null) {
        $jsonError = json_last_error_msg();
        error_log("JSON decode error: " . $jsonError);
        sendJsonResponse(false, 'Некорректные данные: ' . $jsonError, 400);
    }

    // Получаем и валидируем данные из JSON
    $username = isset($data['username']) ? trim($data['username']) : '';
    $email = isset($data['email']) ? trim($data['email']) : '';
    $password = isset($data['password']) ? $data['password'] : '';
    $confirmPassword = isset($data['confirm_password']) ? $data['confirm_password'] : '';

    error_log("Step 3 - Extracted data:");
    error_log("Username: [$username]");
    error_log("Email: [$email]");
    error_log("Password length: " . strlen($password));
    error_log("Confirm password length: " . strlen($confirmPassword));

    // Проверяем, что все необходимые поля заполнены
    if (empty($username)) {
        error_log("Error: Empty username");
        sendJsonResponse(false, 'Имя пользователя обязательно для заполнения', 400);
    }
    if (empty($email)) {
        error_log("Error: Empty email");
        sendJsonResponse(false, 'Email обязателен для заполнения', 400);
    }
    if (empty($password)) {
        error_log("Error: Empty password");
        sendJsonResponse(false, 'Пароль обязателен для заполнения', 400);
    }
    if (empty($confirmPassword)) {
        error_log("Error: Empty confirm password");
        sendJsonResponse(false, 'Подтверждение пароля обязательно', 400);
    }

    require_once 'db.php';
    error_log("Step 4 - Database connection established");

    // Проверяем, не занят ли email или username
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username OR email = :email");
    $stmt->execute([
        ':username' => $username,
        ':email' => $email
    ]);
    $count = $stmt->fetchColumn();
    error_log("Step 5 - Existing users check. Count: $count");
    
    if ($count > 0) {
        error_log("Error: User already exists - Username: [$username], Email: [$email]");
        sendJsonResponse(false, 'Пользователь с таким именем или email уже существует', 409);
    }

    // Хешируем пароль
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    error_log("Step 6 - Password hashed. Hash length: " . strlen($hashedPassword));

    // Добавляем пользователя в базу данных
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
    error_log("Step 7 - Prepared statement for insert. Username: [$username], Email: [$email]");
    
    $result = $stmt->execute([
        ':username' => $username,
        ':email' => $email,
        ':password' => $hashedPassword
    ]);
    
    error_log("Step 8 - Insert result: " . ($result ? "Success" : "Failed"));
    
    if ($result) {
        error_log("Registration successful - Username: [$username]");
        sendJsonResponse(true, 'Регистрация успешно завершена', 201);
    } else {
        error_log("Registration failed - " . print_r($stmt->errorInfo(), true));
        sendJsonResponse(false, 'Ошибка при регистрации', 500);
    }

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    sendJsonResponse(false, 'Ошибка при регистрации: ' . $e->getMessage(), 500);
} catch (Exception $e) {
    error_log("General error: " . $e->getMessage());
    sendJsonResponse(false, 'Произошла ошибка при регистрации', 500);
}