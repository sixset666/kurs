<?php
session_start();
include("db.php");
include("functions.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
    $survey_id = $_GET["id"];
    $survey = getSurveyById($survey_id, $conn);
    $questions = getQuestionsForSurvey($survey_id, $conn);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo '<pre>';
var_dump($_POST);
echo '</pre>';
    $user_id = $_SESSION["user_id"];
    $survey_id = $_GET["id"];
    $answers = $_POST["answers"];

    // Перебираем ответы и сохраняем их в базу данных
    foreach ($answers as $question_id => $answer_text) {
        saveUserResponse($user_id, $survey_id, $question_id, $answer_text, $conn);
    }

    // Перенаправляем пользователя на страницу с результатами
    header("Location: view_results.php?id=$survey_id");
    exit();
}


?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Прохождение опроса</title>
</head>
<body>
<h1><?php echo $survey['title']; ?></h1>
<form action="take_survey.php?id=<?php echo $survey_id; ?>" method="post">
    <?php foreach ($questions as $question): ?>
        <div>
            <p><?php echo $question['text']; ?></p>
            <?php foreach ($question['answers'] as $answer): ?>
                <label>
                    <input type="checkbox" name="answers[<?php echo $question['id']; ?>][]" value="<?php echo $answer['id']; ?>">
                    <?php echo $answer['text']; ?>
                </label>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
    <button type="submit">Отправить ответы</button>
</form>



    <script>
        function addAnswer(questionId) {
    const questionDiv = document.getElementById(questionId);

    const answerCount = questionDiv.querySelectorAll(`select[name^="answers[${questionId}]"]`).length + 1;

    const answerLabel = document.createElement('label');
    answerLabel.textContent = `Вариант ответа ${answerCount}:`;
    questionDiv.appendChild(answerLabel);

    const answerSelect = document.createElement('select');
    answerSelect.name = `answers[${questionId}][]`;

    // Добавьте варианты ответа в список
    const option1 = document.createElement('option');
    option1.value = `option${answerCount}_1`;
    option1.textContent = `Вариант ответа ${answerCount} - 1`;
    answerSelect.appendChild(option1);

    const option2 = document.createElement('option');
    option2.value = `option${answerCount}_2`;
    option2.textContent = `Вариант ответа ${answerCount} - 2`;
    answerSelect.appendChild(option2);

    // Добавьте другие варианты ответа, если необходимо

    questionDiv.appendChild(answerSelect);

    const br = document.createElement('br');
    questionDiv.appendChild(br);
}
    </script>
</body>
</html>
