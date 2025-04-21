<?php
require_once '../db.php';

try {
    // Создаем таблицу рецептов
    $pdo->exec("CREATE TABLE IF NOT EXISTS recipes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        cooking_time INT NOT NULL, -- время приготовления в минутах
        difficulty ENUM('easy', 'medium', 'hard') NOT NULL,
        servings INT NOT NULL, -- количество порций
        image_url VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // Создаем таблицу ингредиентов
    $pdo->exec("CREATE TABLE IF NOT EXISTS ingredients (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL UNIQUE
    )");

    // Создаем таблицу для связи рецептов и ингредиентов
    $pdo->exec("CREATE TABLE IF NOT EXISTS recipe_ingredients (
        recipe_id INT NOT NULL,
        ingredient_id INT NOT NULL,
        amount FLOAT NOT NULL,
        unit VARCHAR(50) NOT NULL,
        PRIMARY KEY (recipe_id, ingredient_id),
        FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
        FOREIGN KEY (ingredient_id) REFERENCES ingredients(id) ON DELETE CASCADE
    )");

    // Создаем таблицу для шагов приготовления
    $pdo->exec("CREATE TABLE IF NOT EXISTS recipe_steps (
        id INT AUTO_INCREMENT PRIMARY KEY,
        recipe_id INT NOT NULL,
        step_number INT NOT NULL,
        description TEXT NOT NULL,
        image_url VARCHAR(255),
        FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
    )");

    echo json_encode(['success' => true, 'message' => 'Таблицы для рецептов успешно созданы']);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?> 