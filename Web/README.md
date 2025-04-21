# Кулинарный помощник

## Установка и запуск

1. Убедитесь, что у вас установлен XAMPP с поддержкой:
   - Apache
   - MySQL
   - PHP 8.2+

2. Скопируйте файлы проекта в директорию:
   ```
   C:\xampp\htdocs\course\
   ```
   или
   ```
   D:\xamp\htdocs\course\
   ```

3. Запустите XAMPP Control Panel и активируйте:
   - Apache
   - MySQL

4. Откройте браузер и перейдите по адресу:
   ```
   http://localhost/course/
   ```

## Важно
- Не используйте Live Server или другие локальные серверы разработки
- Сайт должен открываться только через Apache (http://localhost/course/)
- База данных работает на порту 3306

## Структура проекта
```
course/
├── js/
│   └── scripts.js
├── logs/
│   └── php_error.log
├── config/
├── api/
├── db/
├── index.html
├── main.css
├── register.php
├── login.php
└── logout.php
``` 