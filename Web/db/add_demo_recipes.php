<?php
session_start();
require_once '../db.php';

// Создаем тестового пользователя, если его нет
try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
    $stmt->execute([':username' => 'demo_chef']);
    $user_id = $stmt->fetchColumn();

    if (!$user_id) {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $stmt->execute([
            ':username' => 'demo_chef',
            ':email' => 'demo@example.com',
            ':password' => password_hash('demo123', PASSWORD_DEFAULT)
        ]);
        $user_id = $pdo->lastInsertId();
    }

    // Массив с демонстрационными рецептами
    $recipes = [
        [
            'title' => 'Борщ классический',
            'description' => 'Традиционный украинский борщ со свежей зеленью и сметаной',
            'category_id' => 1, // Первые блюда
            'calories' => 350,
            'cooking_time' => '120',
            'image_url' => 'images/borsch.jpg',
            'ingredients' => [
                ['name' => 'Свекла', 'amount' => 500, 'unit' => 'г'],
                ['name' => 'Капуста', 'amount' => 300, 'unit' => 'г'],
                ['name' => 'Картофель', 'amount' => 400, 'unit' => 'г'],
                ['name' => 'Морковь', 'amount' => 200, 'unit' => 'г'],
                ['name' => 'Лук', 'amount' => 200, 'unit' => 'г'],
                ['name' => 'Говядина', 'amount' => 500, 'unit' => 'г']
            ],
            'steps' => [
                ['description' => 'Сварить мясной бульон', 'image_url' => 'images/borsch_step1.jpg'],
                ['description' => 'Нарезать овощи и обжарить свеклу', 'image_url' => 'images/borsch_step2.jpg'],
                ['description' => 'Добавить овощи в бульон и варить до готовности', 'image_url' => 'images/borsch_step3.jpg']
            ]
        ],
        [
            'title' => 'Цезарь с курицей',
            'description' => 'Классический салат Цезарь с куриным филе и хрустящими гренками',
            'category_id' => 2, // Салаты
            'calories' => 450,
            'cooking_time' => '30',
            'image_url' => 'images/caesar.jpg',
            'ingredients' => [
                ['name' => 'Куриное филе', 'amount' => 300, 'unit' => 'г'],
                ['name' => 'Салат романо', 'amount' => 200, 'unit' => 'г'],
                ['name' => 'Гренки', 'amount' => 100, 'unit' => 'г'],
                ['name' => 'Пармезан', 'amount' => 50, 'unit' => 'г'],
                ['name' => 'Соус Цезарь', 'amount' => 100, 'unit' => 'мл']
            ],
            'steps' => [
                ['description' => 'Приготовить куриное филе на гриле', 'image_url' => 'images/caesar_step1.jpg'],
                ['description' => 'Нарезать салат и смешать с соусом', 'image_url' => 'images/caesar_step2.jpg'],
                ['description' => 'Добавить курицу, гренки и тертый пармезан', 'image_url' => 'images/caesar_step3.jpg']
            ]
        ]
    ];

    // Добавляем каждый рецепт
    foreach ($recipes as $recipe_data) {
        $pdo->beginTransaction();

        try {
            // Добавляем основную информацию о рецепте
            $stmt = $pdo->prepare("INSERT INTO recipes (title, description, category_id, calories, cooking_time, image_url) 
                                 VALUES (:title, :description, :category_id, :calories, :cooking_time, :image_url)");
            
            $stmt->execute([
                ':title' => $recipe_data['title'],
                ':description' => $recipe_data['description'],
                ':category_id' => $recipe_data['category_id'],
                ':calories' => $recipe_data['calories'],
                ':cooking_time' => $recipe_data['cooking_time'],
                ':image_url' => $recipe_data['image_url']
            ]);

            $recipe_id = $pdo->lastInsertId();

            // Добавляем ингредиенты
            foreach ($recipe_data['ingredients'] as $ingredient) {
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
            foreach ($recipe_data['steps'] as $index => $step) {
                $stmt = $pdo->prepare("INSERT INTO recipe_steps (recipe_id, step_number, description, image_url) 
                                     VALUES (:recipe_id, :step_number, :description, :image_url)");
                $stmt->execute([
                    ':recipe_id' => $recipe_id,
                    ':step_number' => $index + 1,
                    ':description' => $step['description'],
                    ':image_url' => $step['image_url']
                ]);
            }

            $pdo->commit();
            echo "Рецепт '{$recipe_data['title']}' успешно добавлен<br>";

        } catch (Exception $e) {
            $pdo->rollBack();
            echo "Ошибка при добавлении рецепта '{$recipe_data['title']}': " . $e->getMessage() . "<br>";
        }
    }

    echo "Все рецепты успешно добавлены!";

} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage();
}
?> 