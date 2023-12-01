<?php
session_start();
include("functions.php");

// Проверка, авторизован ли пользователь
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            flex-direction:column;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
        }

        p {
            text-align: center;
            margin-bottom: 16px;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Личный кабинет</h1>
        <p>Добро пожаловать, <?php echo $_SESSION["username"]; ?>!</p>

        <?php if ($_SESSION["role"] == "admin"): ?>
            <p><a href="admin_profile.php">Перейти к личному кабинету администратора</a></p>
        <?php endif; ?>

        <p><a href="user_profile.php">Перейти к личному кабинету пользователя</a></p>
        <p><a href="logout.php">Выйти</a></p>
    </div>
</body>
</html>
