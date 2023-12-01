<?php
session_start();
include("functions.php");

// Проверка, авторизован ли пользователь
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$userSurveys = getUserSurveys($user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет пользователя</title>
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

        ul {
            list-style: none;
            padding: 0;
            text-align: center;
        }

        li {
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
        <h1>Личный кабинет пользователя</h1>
        <p>Добро пожаловать, <?php echo $_SESSION["username"]; ?>!</p>

        <h2>Пройденные опросы:</h2>
        <?php if (empty($userSurveys)): ?>
            <p>Вы еще не прошли ни одного опроса.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($userSurveys as $survey): ?>
                    <li><?php echo $survey["title"]; ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <p><a href="dashboard.php">Вернуться в личный кабинет</a></p>
        <p><a href="logout.php">Выйти</a></p>
    </div>
</body>
</html>
