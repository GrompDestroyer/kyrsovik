<?php
header('Content-Type: application/json');
require_once '../config/db.php';

$search = isset($_GET['query']) ? $_GET['query'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$calories = isset($_GET['calories']) ? $_GET['calories'] : '';

try {
    $params = [];
    $conditions = [];
    
    if ($search) {
        $conditions[] = "(r.title LIKE :search OR i.name LIKE :search)";
        $params[':search'] = "%$search%";
    }
    
    if ($category) {
        $conditions[] = "c.name = :category";
        $params[':category'] = $category;
    }
    
    if ($calories) {
        switch($calories) {
            case 'low':
                $conditions[] = "r.calories < 300";
                break;
            case 'medium':
                $conditions[] = "r.calories BETWEEN 300 AND 600";
                break;
            case 'high':
                $conditions[] = "r.calories > 600";
                break;
        }
    }
    
    $whereClause = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
    
    $query = "SELECT DISTINCT r.*, c.name as category, GROUP_CONCAT(i.name) as ingredients
            FROM recipes r
            LEFT JOIN categories c ON r.category_id = c.id
            LEFT JOIN recipe_ingredients ri ON r.id = ri.recipe_id
            LEFT JOIN ingredients i ON ri.ingredient_id = i.id
            $whereClause
            GROUP BY r.id";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    
    $recipes = [];
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $recipes[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'description' => $row['description'],
            'category' => $row['category'],
            'calories' => (int)$row['calories'],
            'time' => $row['cooking_time'],
            'image' => $row['image_url'],
            'ingredients' => $row['ingredients'] ? explode(',', $row['ingredients']) : []
        ];
    }
    
    echo json_encode(['success' => true, 'data' => $recipes]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}