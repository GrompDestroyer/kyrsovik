<?php
require_once '../db.php';

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID рецепта не указан']);
    exit;
}

try {
    $recipeId = (int)$_GET['id'];
    
    // Получаем основную информацию о рецепте
    $stmt = $pdo->prepare("
        SELECT r.*, u.username as author
        FROM recipes r
        JOIN users u ON r.user_id = u.id
        WHERE r.id = :id
    ");
    $stmt->execute([':id' => $recipeId]);
    $recipe = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$recipe) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Рецепт не найден']);
        exit;
    }

    // Получаем ингредиенты
    $stmt = $pdo->prepare("
        SELECT i.name, ri.amount, ri.unit
        FROM recipe_ingredients ri
        JOIN ingredients i ON ri.ingredient_id = i.id
        WHERE ri.recipe_id = :recipe_id
    ");
    $stmt->execute([':recipe_id' => $recipeId]);
    $recipe['ingredients'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Получаем шаги приготовления
    $stmt = $pdo->prepare("
        SELECT step_number, description, image_url
        FROM recipe_steps
        WHERE recipe_id = :recipe_id
        ORDER BY step_number
    ");
    $stmt->execute([':recipe_id' => $recipeId]);
    $recipe['steps'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $recipe
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ошибка при получении рецепта: ' . $e->getMessage()]);
}
?> 