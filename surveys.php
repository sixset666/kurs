<?php
session_start();
include("functions.php");

// Проверка, авторизован ли пользователь
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$surveys = getSurveys();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список опросов</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction:column;
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
        <h1>Список опросов</h1>

        <ul>
            <?php foreach ($surveys as $survey): ?>
                <li>
                    <?php echo $survey["title"]; ?>
                    <a href="take_survey.php?survey_id=<?php echo $survey["id"]; ?>">Пройти опрос</a>
                </li>
            <?php endforeach; ?>
        </ul>

        <p><a href="dashboard.php">Вернуться в личный кабинет</a></p>
        <p><a href="logout.php">Выйти</a></p>
    </div>
</body>
</html>
