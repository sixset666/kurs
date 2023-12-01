<?php
session_start();
include("db.php");
include("functions.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$surveys = getAllSurveys($conn); // Функция, чтобы получить все опросы

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список опросов</title>
</head>
<body>
    <h1>Список опросов</h1>
    <ul>
        <?php foreach ($surveys as $survey): ?>
            <li>
                <a href="take_survey.php?id=<?php echo $survey['id']; ?>"><?php echo $survey['title']; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
