<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once 'config/database.php';
require_once 'models/User.php';
require_once 'logger.php';

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = 'Пожалуйста, заполните все поля.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Введите корректный email.';
    } else {
        $user = User::findByEmailWithHash($email);

        if ($user === null || !password_verify($password, $user['password_hash'])) {
            writeLog($email, 'FAIL_LOGIN');
            $error = 'Неверный email или пароль.';
        } else {
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            writeLog($email, 'SUCCESS_LOGIN');
            header('Location: dashboard.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Click Store — Авторизация</title>
</head>
<body>

    <ul class="nav">
        <li><a href="index.html">Главная</a></li>
        <li><a href="catalog.html">Каталог</a></li>
        <li><a href="login.php">Войти</a></li>
    </ul>

    <hr>

    <h1>Авторизация</h1>

    <div class="auth-container">
        <?php if ($error !== ''): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" class="form-input"
                       value="<?php echo htmlspecialchars($email); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" class="form-input" required>
            </div>

            <button type="submit" class="btn-login">Войти</button>
        </form>
    </div>

    <hr>

    <p>&copy; Click Store. Все права защищены.</p>

</body>
</html>
