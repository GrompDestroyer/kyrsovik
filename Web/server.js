const express = require('express');
const bodyParser = require('body-parser');
const db = require('./database');

const app = express();

app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

app.get('/', (req, res) => {
  res.send(`
    <h1>Кулинарный помощник</h1>
    <button onclick="location.href='/register'">Регистрация</button>
    <button onclick="location.href='/login'">Вход</button>
  `);
});

app.get('/register', (req, res) => {
  res.send(`
    <h2>Регистрация</h2>
    <form action="/register" method="post">
      <input type="text" name="username" placeholder="Имя пользователя" required>
      <input type="password" name="password" placeholder="Пароль" required>
      <button type="submit">Зарегистрироваться</button>
    </form>
  `);
});

app.post('/register', (req, res) => {
  const { username, password } = req.body;
  const stmt = db.prepare("INSERT INTO users (username, password) VALUES (?, ?)");
  stmt.run(username, password, function(err) {
    if (err) {
      return res.status(500).send("Ошибка регистрации");
    }
    res.send("Регистрация успешна");
  });
  stmt.finalize();
});

app.get('/login', (req, res) => {
  res.send(`
    <h2>Вход</h2>
    <form action="/login" method="post">
      <input type="text" name="username" placeholder="Имя пользователя" required>
      <input type="password" name="password" placeholder="Пароль" required>
      <button type="submit">Войти</button>
    </form>
  `);
});

app.post('/login', (req, res) => {
  const { username, password } = req.body;
  db.get("SELECT * FROM users WHERE username = ? AND password = ?", [username, password], (err, row) => {
    if (err) {
      return res.status(500).send("Ошибка входа");
    }
    if (row) {
      res.send("Вход успешен");
    } else {
      res.send("Неверное имя пользователя или пароль");
    }
  });
});

app.listen(3000, () => {
  console.log('Сервер запущен на http://localhost:3000');
});