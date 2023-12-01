<?php
session_start();
include("db.php");
include("functions.php");

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION["user_id"];
$adminSurveys = getAdminSurveys($admin_id, $conn);


if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION["user_id"];
$adminSurveys = getAdminSurveys($admin_id, $conn);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет администратора</title>
</head>
<style>
  body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1, h2, p {
            color: #333;
        }

        .main-list {
            list-style-type: none;
            padding: 0;
        }

        .main-list li {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4285f4;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #1c6df1;
        }

</style>
<body>
    <div class="container">
        <h1>Личный кабинет администратора</h1>
        <p>Добро пожаловать, <?php echo $_SESSION["username"]; ?>!</p>

        <h2>Добавленные опросы:</h2>
        <?php if (empty($adminSurveys)): ?>
            <p>Вы еще не добавили ни одного опроса.</p>
        <?php else: ?>
            <ul class="main-list">
                <?php foreach ($adminSurveys as $survey): ?>
                    <li>
                        <strong><?php echo $survey["title"]; ?></strong>
                        <br>
                        <?php echo isset($survey["description"]) ? $survey["description"] : ''; ?>
                        <br>
                        <strong>Вопросы:</strong>
                        <ul>
                            <?php
                            $questions = getQuestionsForSurvey($survey["id"], $conn);
                            foreach ($questions as $question): ?>
                                <li>
                                    <?php echo $question["text"]; ?>
                                    <br>
                                    <strong>Ответы:</strong>
                                    <ul>
                                        <?php
                                        $answers = getAnswersForQuestion($question["id"], $conn);
                                        foreach ($answers as $answer): ?>
                                            <li class="answer"><?php echo $answer["text"]; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <!-- Кнопки для редактирования, удаления и просмотра опроса -->
                        <div class="btns">
                            <a class="btn" href="edit_survey.php?id=<?php echo $survey["id"]; ?>">Редактировать</a>
                            <a class="btn" href="delete_survey.php?id=<?php echo $survey["id"]; ?>">Удалить</a>
                            <a class="btn" href="view_survey.php?id=<?php echo $survey["id"]; ?>">Просмотреть опрос</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <p><a class="btn" href="dashboard.php">Назад к панели управления</a></p>
        <p><a class="btn" href="logout.php">Выйти</a></p>
    </div>
</body>
</html>
