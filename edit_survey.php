<?php
session_start();
include("db.php");
include("functions.php");

// Проверка, авторизован ли пользователь и является ли он администратором
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
    $survey_id = $_GET["id"];
    $survey = getSurveyById($survey_id, $conn);
    $questions = getQuestionsForSurvey($survey_id, $conn);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $survey_id = $_GET["id"];
    $title = $_POST["title"];
    $description = $_POST["description"];

    // Обновление опроса
    updateSurvey($survey_id, $title, $description, $conn);

    // Обновление вопросов
    foreach ($_POST["questions"] as $question_id => $question_text) {
        updateQuestion($question_id, $question_text, $conn);
    }

    // Обновление ответов
    foreach ($_POST["answers"] as $question_id => $answers) {
        foreach ($answers as $answer_id => $answer_text) {
            updateAnswer($answer_id, $answer_text, $conn);
        }
    }

    // После обновления, перенаправьте пользователя на admin_profile.php
    header("Location: admin_profile.php");
    exit();
}

?>

<!-- edit_survey.php -->

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование опроса</title>
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

    h1 {
        color: #333;
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    input, textarea {
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
        box-sizing: border-box;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .question {
        margin-bottom: 20px;
        padding: 15px;
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    button {
        background-color: #4caf50;
        color: #fff;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    button:hover {
        background-color: #45a049;
    }
</style>

</head>
<body>
    <div class="container">
        <h1>Редактирование опроса</h1>
        <?php if (isset($survey) && !empty($survey)): ?>
            
            <form action="edit_survey.php?id=<?php echo $survey_id; ?>" method="post">
                <label for="title">Название опроса:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($survey['title']); ?>" required>

                <label for="description">Описание опроса:</label>
                <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($survey['description']); ?></textarea>

                <h2>Вопросы и ответы:</h2>
                <div id="questions-container">
                    <?php foreach ($questions as $question): ?>
                        <div class="question">
                            <label for="question_<?php echo $question['id']; ?>">Вопрос <?php echo $question['id']; ?>:</label>
                            <input type="text" id="question_<?php echo $question['id']; ?>" name="questions[<?php echo $question['id']; ?>]" value="<?php echo htmlspecialchars($question['text']); ?>" required>

                            <!-- Вывод ответов для текущего вопроса -->
                            <?php
                            $answers = getAnswersForQuestion($question['id'], $conn);
                            foreach ($answers as $answer): ?>
                                <label for="answer_<?php echo $answer['id']; ?>">Ответ <?php echo $answer['id']; ?>:</label>
                                <input type="text" id="answer_<?php echo $answer['id']; ?>" name="answers[<?php echo $question['id']; ?>][<?php echo $answer['id']; ?>]" value="<?php echo htmlspecialchars($answer['text']); ?>" required>
                            <?php endforeach; ?>

                            <button type="button" onclick="addAnswer(<?php echo $question['id']; ?>)">Добавить ответ</button>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button type="button" onclick="addQuestion()">Добавить вопрос</button>
                <button type="submit">Сохранить изменения</button>
            </form>

        <?php else: ?>
            <p>Опрос не найден.</p>
        <?php endif; ?>

        <p><a class="btn" href="admin_profile.php">Назад к опросам</a></p>
    </div>

    <script>
        function addQuestion() {
            // Добавьте код для добавления нового вопроса в интерфейсе редактирования
            const container = document.getElementById('questions-container');
            const questionCount = container.children.length + 1;

            const questionDiv = document.createElement('div');
            questionDiv.className = 'question';

            const questionLabel = document.createElement('label');
            questionLabel.textContent = `Вопрос ${questionCount}:`;
            questionDiv.appendChild(questionLabel);

            const questionInput = document.createElement('input');
            questionInput.type = 'text';
            questionInput.name = `questions[]`;
            questionInput.required = true;
            questionDiv.appendChild(questionInput);

            const answerLabel = document.createElement('label');
            answerLabel.textContent = `Ответ 1:`;
            questionDiv.appendChild(answerLabel);

            const answerInput = document.createElement('input');
            answerInput.type = 'text';
            answerInput.name = `answers[${questionCount}][1]`;
            answerInput.required = true;
            questionDiv.appendChild(answerInput);

            const addButton = document.createElement('button');
            addButton.type = 'button';
            addButton.textContent = 'Добавить ответ';
            addButton.onclick = function() {
                addAnswer(questionDiv, questionCount);
            };
            questionDiv.appendChild(addButton);

            container.appendChild(questionDiv);
        }

        function addAnswer(questionDiv, questionCount) {
            const answerCount = questionDiv.querySelectorAll(`input[name^="answers[${questionCount}]"]`).length + 1;

            const answerLabel = document.createElement('label');
            answerLabel.textContent = `Ответ ${answerCount}:`;
            questionDiv.appendChild(answerLabel);

            const answerInput = document.createElement('input');
            answerInput.type = 'text';
            answerInput.name = `answers[${questionCount}][${answerCount}]`;
            answerInput.required = true;
            questionDiv.appendChild(answerInput);

            const br = document.createElement('br');
            questionDiv.appendChild(br);
        }
    </script>
</body>
</html>
