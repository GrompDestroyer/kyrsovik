const recipes = [
    {
        name: "Салат Цезарь",
        ingredients: [
            { name: "Курица", calories: 200 },
            { name: "Салат", calories: 50 },
            { name: "Сыр", calories: 100 },
            { name: "Соус", calories: 150 }
        ]
    },
    {
        name: "Борщ",
        ingredients: [
            { name: "Свекла", calories: 40 },
            { name: "Картофель", calories: 80 },
            { name: "Мясо", calories: 250 },
            { name: "Капуста", calories: 30 }
        ]
    },
    {
        name: "Паста Карбонара",
        ingredients: [
            { name: "Спагетти", calories: 200 },
            { name: "Бекон", calories: 150 },
            { name: "Яйца", calories: 70 },
            { name: "Сыр Пармезан", calories: 100 }
        ]
    },
    {
        name: "Омлет с овощами",
        ingredients: [
            { name: "Яйца", calories: 150 },
            { name: "Помидоры", calories: 20 },
            { name: "Перец", calories: 15 },
            { name: "Лук", calories: 10 }
        ]
    }
];

function displayRecipes() {
    const recipeList = document.getElementById('recipes');
    recipeList.innerHTML = '';
    recipes.forEach((recipe, index) => {
        const li = document.createElement('li');
        li.textContent = recipe.name;
        li.onclick = () => showRecipeDetails(index);
        recipeList.appendChild(li);
    });
}

function showRecipeDetails(index) {
    const recipe = recipes[index];
    const recipeDetails = document.getElementById('recipe-details');
    const recipeList = document.getElementById('recipe-list');
    const recipeInfo = document.getElementById('recipe-info');

    recipeInfo.innerHTML = `<h3>${recipe.name}</h3>`;
    let totalCalories = 0;
    recipe.ingredients.forEach(ingredient => {
        recipeInfo.innerHTML += `<p>${ingredient.name}: ${ingredient.calories} калорий</p>`;
        totalCalories += ingredient.calories;
    });
    recipeInfo.innerHTML += `<p><strong>Всего калорий: ${totalCalories}</strong></p>`;

    recipeList.style.display = 'none';
    recipeDetails.style.display = 'block';
}

function goBack() {
    document.getElementById('recipe-list').style.display = 'block';
    document.getElementById('recipe-details').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', displayRecipes);
function showSection(sectionId) {
    document.querySelectorAll('section').forEach(section => {
        section.style.display = 'none';
    });
    document.getElementById(sectionId).style.display = 'block';
}

document.getElementById('registerForm').addEventListener('submit', function(event) {
    event.preventDefault();
    // Здесь будет логика регистрации
    alert('Регистрация прошла успешно!');
    goBack();
});

document.getElementById('resetPasswordForm').addEventListener('submit', function(event) {
    event.preventDefault();
    // Здесь будет логика восстановления пароля
    alert('Инструкции по восстановлению пароля отправлены на ваш email!');
    goBack();
});

function goBack() {
    showSection('recipe-list');
}

document.addEventListener('DOMContentLoaded', function() {
    displayRecipes();
    showSection('recipe-list');
});
function showSection(sectionId) {
    document.querySelectorAll('section').forEach(section => {
        section.style.display = 'none';
    });
    document.getElementById(sectionId).style.display = 'block';

    // Скрыть кнопку "Админ-панель", если мы находимся в админ-панели
    const adminButton = document.getElementById('adminButton');
    if (sectionId === 'admin-panel') {
        adminButton.style.display = 'none';
    } else {
        adminButton.style.display = 'inline-block';
    }
}

document.getElementById('registerForm').addEventListener('submit', function(event) {
    event.preventDefault();
    alert('Регистрация прошла успешно!');
    goBack();
});

document.getElementById('resetPasswordForm').addEventListener('submit', function(event) {
    event.preventDefault();
    alert('Инструкции по восстановлению пароля отправлены на ваш email!');
    goBack();
});

function goBack() {
    showSection('recipe-list');
}

document.addEventListener('DOMContentLoaded', function() {
    displayRecipes();
    showSection('recipe-list');
});

document.getElementById('loginButton').addEventListener('click', function() {
    // Открыть модальное окно для входа
    alert('Открыть окно входа');
});

document.addEventListener('DOMContentLoaded', function() {
    const loginModal = document.getElementById('loginModal');
    const loginButton = document.getElementById('loginButton');
    const closeModal = document.getElementsByClassName('close')[0];

    loginButton.onclick = function() {
        loginModal.style.display = 'block';
    }

    closeModal.onclick = function() {
        loginModal.style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target == loginModal) {
            loginModal.style.display = 'none';
        }
    }

    document.getElementById('loginForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const username = document.getElementById('loginUsername').value;
        const password = document.getElementById('loginPassword').value;
        
        // Здесь вы можете добавить логику для отправки данных на сервер
        alert('Вход выполнен!');
        loginModal.style.display = 'none';
    });
});