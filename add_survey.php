<?php
session_start();
include("db.php");
include("functions.php");

// Проверка, авторизован ли пользователь и является ли он администратором
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $description = $_POST["description"];
    $questions = $_POST["questions"];
    $answers = $_POST["answers"];

    $admin_id = $_SESSION["user_id"];
    $survey_id = saveSurvey($admin_id, $title, $description, $conn);

    // Массив для хранения созданных вопросов
    $createdQuestions = array();

    // Сохранение вопросов и ответов
    foreach ($questions as $index => $questionText) {
        $question_id = saveQuestion($survey_id, $questionText, $conn);
        $createdQuestions[] = $question_id;

        // Проверка наличия ответов для данного вопроса
        if (isset($answers[$index])) {
            foreach ($answers[$index] as $answerText) {
                saveAnswer($question_id, $answerText, $conn);
            }
        }
    }

    // Можно добавить дополнительные действия после успешного добавления опроса
    header("Location: admin_profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет администратора</title>
    <style>
        .container {
            max-width: 600px;
            margin: 0 auto;
        }

        .question {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Добавление опроса</h1>
        <form action="add_survey.php" method="post" id="surveyForm">
            <label for="title">Название опроса:</label>
            <input type="text" id="title" name="title" required>

            <label for="description">Описание опроса:</label>
            <textarea id="description" name="description" rows="4"></textarea>

            <h2>Вопросы и ответы:</h2>
            <div id="questions-container">
                <div class="question">
                    <label for="question1">Вопрос 1:</label>
                    <input type="text" id="question1" name="questions[]" required>

                    <label for="answer1_1">Ответ 1:</label>
                    <input type="text" name="answers[0][]" required>

                    <button type="button" onclick="addAnswer(0)">Добавить ответ</button>
                </div>
            </div>

            <button type="button" onclick="addQuestion()">Добавить вопрос</button>
            <button type="submit">Создать опрос</button>
        </form>
    </div>

    <script>
        function addQuestion() {
            const container = document.getElementById('questions-container');
            const questionCount = container.children.length + 1;

            const questionDiv = document.createElement('div');
            questionDiv.className = 'question';

            const questionLabel = document.createElement('label');
            questionLabel.textContent = `Вопрос ${questionCount}:`;
            questionDiv.appendChild(questionLabel);

            const questionInput = document.createElement('input');
            questionInput.type = 'text';
            questionInput.name = 'questions[]';
            questionInput.required = true;
            questionDiv.appendChild(questionInput);

            const answerLabel = document.createElement('label');
            answerLabel.textContent = `Ответ 1:`;
            questionDiv.appendChild(answerLabel);

            const answerInput = document.createElement('input');
            answerInput.type = 'text';
            answerInput.name = `answers[${questionCount - 1}][]`;
            questionDiv.appendChild(answerInput);

            const addButton = document.createElement('button');
            addButton.type = 'button';
            addButton.textContent = 'Добавить ответ';
            addButton.onclick = function() {
                addAnswer(questionCount - 1);
            };
            questionDiv.appendChild(addButton);

            container.appendChild(questionDiv);
        }

        function addAnswer(questionIndex) {
            const container = document.getElementById('questions-container');
            const questionDiv = container.children[questionIndex];

            const answerCount = questionDiv.querySelectorAll('input[name^="answers"]').length + 1;

            const answerLabel = document.createElement('label');
            answerLabel.textContent = `Ответ ${answerCount}:`;
            questionDiv.appendChild(answerLabel);

            const answerInput = document.createElement('input');
            answerInput.type = 'text';
            answerInput.name = `answers[${questionIndex}][]`;
            questionDiv.appendChild(answerInput);
        }
    </script>
</body>
</html>
