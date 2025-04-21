<?php
require_once '../db.php';

try {
    // Читаем SQL файл
    $sql = file_get_contents(__DIR__ . '/create_tables.sql');
    
    // Выполняем SQL
    $pdo->exec($sql);
    
    echo "База данных успешно настроена!\n";
    
} catch (PDOException $e) {
    die("Ошибка настройки базы данных: " . $e->getMessage() . "\n");
} 