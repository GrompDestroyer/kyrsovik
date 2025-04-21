// Дожидаемся полной загрузки DOM
document.addEventListener('DOMContentLoaded', function() {
    // Получаем элементы модальных окон
    const loginModal = document.getElementById('loginModal');
    const registerModal = document.getElementById('registerModal');
    
    // Кнопки открытия модальных окон
    const loginBtn = document.getElementById('loginBtn');
    const registerBtn = document.getElementById('registerBtn');
    
    // Кнопки закрытия модальных окон
    const closeLoginBtn = document.getElementById('closeLoginModal');
    const closeRegisterBtn = document.getElementById('closeRegisterModal');
    
    // Открытие модальных окон
    loginBtn.addEventListener('click', () => {
        loginModal.style.display = 'block';
    });
    
    registerBtn.addEventListener('click', () => {
        registerModal.style.display = 'block';
    });
    
    // Закрытие модальных окон
    closeLoginBtn.addEventListener('click', () => {
        loginModal.style.display = 'none';
    });
    
    closeRegisterBtn.addEventListener('click', () => {
        registerModal.style.display = 'none';
    });
    
    // Закрытие при клике вне модального окна
    window.addEventListener('click', (event) => {
        if (event.target === loginModal) {
            loginModal.style.display = 'none';
        }
        if (event.target === registerModal) {
            registerModal.style.display = 'none';
        }
    });

    // Функция для регистрации пользователя
    async function registerUser(event) {
        event.preventDefault();
        
        const form = event.target;
        const formData = {
            username: document.getElementById('registerUsername').value,
            email: document.getElementById('registerEmail').value,
            password: document.getElementById('registerPassword').value,
            confirm_password: document.getElementById('confirmPassword').value
        };

        try {
            const response = await fetch('register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();
            
            if (data.success) {
                alert(data.message);
                registerModal.style.display = 'none';
                form.reset();
            } else {
                alert(data.message || 'Произошла ошибка при регистрации');
            }
        } catch (error) {
            console.error('Ошибка:', error);
            alert('Произошла ошибка при регистрации');
        }
    }

    // Функция для входа пользователя
    async function loginUser(event) {
        event.preventDefault();
        
        const form = event.target;
        const formData = {
            username: document.getElementById('loginUsername').value,
            password: document.getElementById('loginPassword').value
        };

        try {
            const response = await fetch('login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();
            console.log('Login response:', data);
            
            if (data.success) {
                alert(data.message);
                loginModal.style.display = 'none';
                loginBtn.style.display = 'none';
                registerBtn.style.display = 'none';
                document.getElementById('logoutBtn').style.display = 'block';
                form.reset();
            } else {
                alert(data.message || 'Ошибка входа');
            }
        } catch (error) {
            console.error('Ошибка:', error);
            alert('Произошла ошибка при входе');
        }
    }

    // Функция для добавления нового ингредиента
    function addIngredient() {
        const ingredientsList = document.getElementById('ingredientsList');
        const newIngredient = document.createElement('div');
        newIngredient.className = 'ingredient-item';
        newIngredient.innerHTML = `
            <div class="form-group">
                <label>Ингредиент:</label>
                <input type="text" class="ingredient-name" placeholder="Название ингредиента" required>
            </div>
            <div class="form-group">
                <label>Количество (г):</label>
                <input type="number" class="ingredient-weight" min="1" required>
            </div>
            <div class="form-group">
                <label>Калорийность (ккал/100г):</label>
                <input type="number" class="ingredient-calories" min="0" required>
            </div>
        `;
        ingredientsList.appendChild(newIngredient);
    }

    // Функция для расчета калорий
    function calculateCalories(event) {
        event.preventDefault();
        
        const ingredients = document.getElementsByClassName('ingredient-item');
        let totalCalories = 0;
        let breakdown = '';

        for (let item of ingredients) {
            const name = item.querySelector('.ingredient-name').value;
            const weight = parseFloat(item.querySelector('.ingredient-weight').value);
            const caloriesPer100g = parseFloat(item.querySelector('.ingredient-calories').value);
            
            const ingredientCalories = (weight * caloriesPer100g) / 100;
            totalCalories += ingredientCalories;
            
            breakdown += `<p>${name}: ${ingredientCalories.toFixed(1)} ккал</p>`;
        }

        document.getElementById('totalCalories').textContent = totalCalories.toFixed(1);
        document.getElementById('ingredientBreakdown').innerHTML = breakdown;
        document.getElementById('calorieResult').style.display = 'block';
    }

    // Добавляем обработчики событий
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', registerUser);
    }

    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', loginUser);
    }

    const calorieCalculator = document.getElementById('calorieCalculator');
    if (calorieCalculator) {
        calorieCalculator.addEventListener('submit', calculateCalories);
    }

    // Добавляем обработчик для кнопки добавления ингредиента
    const addIngredientBtn = document.querySelector('button[onclick="addIngredient()"]');
    if (addIngredientBtn) {
        addIngredientBtn.onclick = addIngredient;
    }

    // Обработчик для кнопки выхода
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', async function() {
            try {
                const response = await fetch('logout.php');
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('loginBtn').style.display = 'block';
                    document.getElementById('registerBtn').style.display = 'block';
                    document.getElementById('logoutBtn').style.display = 'none';
                }
            } catch (error) {
                console.error('Ошибка:', error);
                alert('Произошла ошибка при выходе');
            }
        });
    }
});