<?php
session_start();
require_once '../db.php';

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Необходима авторизация']);
    exit;
}

// Получаем данные из запроса
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Неверный формат данных']);
    exit;
}

try {
    // Начинаем транзакцию
    $pdo->beginTransaction();

    // Добавляем рецепт
    $stmt = $pdo->prepare("INSERT INTO recipes (user_id, title, description, cooking_time, difficulty, servings, image_url) 
                          VALUES (:user_id, :title, :description, :cooking_time, :difficulty, :servings, :image_url)");
    
    $stmt->execute([
        ':user_id' => $_SESSION['user_id'],
        ':title' => $data['title'],
        ':description' => $data['description'],
        ':cooking_time' => $data['cooking_time'],
        ':difficulty' => $data['difficulty'],
        ':servings' => $data['servings'],
        ':image_url' => $data['image_url'] ?? null
    ]);

    $recipe_id = $pdo->lastInsertId();

    // Добавляем ингредиенты
    foreach ($data['ingredients'] as $ingredient) {
        // Проверяем существует ли ингредиент
        $stmt = $pdo->prepare("SELECT id FROM ingredients WHERE name = :name");
        $stmt->execute([':name' => $ingredient['name']]);
        $ingredient_id = $stmt->fetchColumn();

        // Если ингредиент не существует, создаем его
        if (!$ingredient_id) {
            $stmt = $pdo->prepare("INSERT INTO ingredients (name) VALUES (:name)");
            $stmt->execute([':name' => $ingredient['name']]);
            $ingredient_id = $pdo->lastInsertId();
        }

        // Связываем ингредиент с рецептом
        $stmt = $pdo->prepare("INSERT INTO recipe_ingredients (recipe_id, ingredient_id, amount, unit) 
                              VALUES (:recipe_id, :ingredient_id, :amount, :unit)");
        $stmt->execute([
            ':recipe_id' => $recipe_id,
            ':ingredient_id' => $ingredient_id,
            ':amount' => $ingredient['amount'],
            ':unit' => $ingredient['unit']
        ]);
    }

    // Добавляем шаги приготовления
    foreach ($data['steps'] as $index => $step) {
        $stmt = $pdo->prepare("INSERT INTO recipe_steps (recipe_id, step_number, description, image_url) 
                              VALUES (:recipe_id, :step_number, :description, :image_url)");
        $stmt->execute([
            ':recipe_id' => $recipe_id,
            ':step_number' => $index + 1,
            ':description' => $step['description'],
            ':image_url' => $step['image_url'] ?? null
        ]);
    }

    // Завершаем транзакцию
    $pdo->commit();

    echo json_encode([
        'success' => true, 
        'message' => 'Рецепт успешно добавлен',
        'recipe_id' => $recipe_id
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ошибка при добавлении рецепта: ' . $e->getMessage()]);
}
?> 