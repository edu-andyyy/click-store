<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once 'users.php';
require_once 'logger.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($login === '' || $password === '') {
        $error = 'Пожалуйста, заполните все поля.';
    } elseif (!isset($users[$login])) {
        writeLog($login, 'FAIL_LOGIN');
        $error = 'Неверный логин или пароль.';
    } elseif (!password_verify($password, $users[$login]['password_hash'])) {
        writeLog($login, 'FAIL_LOGIN');
        $error = 'Неверный логин или пароль.';
    } else {
        $_SESSION['user_id'] = $users[$login]['id'];
        $_SESSION['username'] = $login;
        writeLog($login, 'SUCCESS_LOGIN');
        header('Location: dashboard.php');
        exit;
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
                <label for="login">Логин:</label>
                <input type="text" id="login" name="login" class="form-input"
                       value="<?php echo htmlspecialchars($login ?? ''); ?>" required>
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
