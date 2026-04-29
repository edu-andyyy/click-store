<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$username = htmlspecialchars($_SESSION['username']);
$userId = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Click Store — Личный кабинет</title>
</head>
<body>

    <ul class="nav">
        <li><a href="index.html">Главная</a></li>
        <li><a href="catalog.html">Каталог</a></li>
        <li><a href="logout.php">Выйти (<?php echo $username; ?>)</a></li>
    </ul>

    <hr>

    <h1>Личный кабинет</h1>

    <div class="dashboard-container">
        <p class="dashboard-welcome">Добро пожаловать, <b><?php echo $username; ?></b>!</p>

        <div class="dashboard-info">
            <p><b>ID пользователя:</b> <?php echo $userId; ?></p>
            <p><b>Логин:</b> <?php echo $username; ?></p>
        </div>

        <a href="catalog.html" class="btn-login">Перейти в каталог</a>
        <a href="logout.php" class="btn-logout">Выйти из аккаунта</a>
    </div>

    <hr>

    <p>&copy; Click Store. Все права защищены.</p>

</body>
</html>
