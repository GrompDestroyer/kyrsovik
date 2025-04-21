<?php
header('Content-Type: application/json');
require_once '../config/db.php';

$name = isset($_GET['name']) ? $_GET['name'] : '';

try {
    $stmt = $pdo->prepare("SELECT calories_per_100g FROM ingredients WHERE name = :name");
    $stmt->execute([':name' => $name]);
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'calories_per_100g' => (float)$result['calories_per_100g']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Ингредиент не найден'
        ]);
    }
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}