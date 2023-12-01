<?php
session_start();
include("db.php");
include("functions.php");

// Проверка, авторизован ли пользователь и является ли он администратором
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Проверка, является ли пользователь администратором
$admin = false;
if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin") {
    $admin = true;
}

// Проверка наличия параметра id в запросе
if (!isset($_GET["id"])) {
    header("Location: admin_profile.php");
    exit();
}

$survey_id = $_GET["id"];
$survey = getSurveyById($survey_id, $conn);

// Проверка, существует ли опрос с указанным id
if (!$survey) {
    header("Location: admin_profile.php");
    exit();
}

$questions = getQuestionsForSurvey($survey_id, $conn);

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Просмотр опроса</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 800px;
        margin: 20px auto;
        background-color: #fff;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    h1, h2, h3 {
        color: #333;
    }

    p, ul {
        color: #666;
    }

    ul {
        list-style: none;
        padding: 0;
    }

    li {
        margin-bottom: 5px;
    }

    a {
        color: #4caf50;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }
</style>

</head>
<body>
    <div class="container">
        <h1>Просмотр опроса</h1>

        <?php if ($admin): ?>
            <p><a href="admin_profile.php">Назад к списку опросов</a></p>
        <?php endif; ?>

        <h2><?php echo htmlspecialchars($survey["title"]); ?></h2>
        <p><?php echo htmlspecialchars($survey["description"]); ?></p>

        <?php if (!empty($questions)): ?>
            <h3>Вопросы и ответы:</h3>
            <?php foreach ($questions as $question): ?>
                <div class="question">
                    <p><strong>Вопрос:</strong> <?php echo htmlspecialchars($question["text"]); ?></p>
                    <?php
                    $answers = getAnswersForQuestion($question["id"], $conn);
                    if (!empty($answers)): ?>
                        <ul>
                            <?php foreach ($answers as $answer): ?>
                                <li><strong>Ответ:</strong> <?php echo htmlspecialchars($answer["text"]); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Опрос не содержит вопросов.</p>
        <?php endif; ?>
    </div>
</body>
</html>
