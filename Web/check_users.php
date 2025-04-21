<?php
require_once 'db.php';

try {
    // Получаем информацию о структуре таблицы
    $stmt = $pdo->query("DESCRIBE users");
    $structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Получаем все записи из таблицы
    $stmt = $pdo->query("SELECT * FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Выводим результаты
    header('Content-Type: application/json');
    echo json_encode([
        'table_structure' => $structure,
        'users' => $users
    ], JSON_PRETTY_PRINT);
    
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?> 