<?php
include("db.php");

function registerUser($username, $password) {
    global $conn;
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hashed_password);
    $stmt->execute();
    $stmt->close();
}

function loginUser($username, $password) {
    global $conn;

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    
    if (!$stmt) {
        die("Error in loginUser(): " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();

    if ($stmt->error) {
        die("Error in loginUser(): " . $stmt->error);
    }

    $stmt->bind_result($user_id, $db_username, $db_password, $role);
    $stmt->fetch();
    $stmt->close();

    if (password_verify($password, $db_password)) {
        $_SESSION["user_id"] = $user_id;
        $_SESSION["username"] = $db_username;
        $_SESSION["role"] = $role;
        return true;
    } else {
        return false;
    }
}




if (!function_exists('getAdminSurveys')) {
    function getAdminSurveys($admin_id, $conn) {
        $sql = "SELECT * FROM surveys WHERE admin_id = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Error in getAdminSurveys(): " . $conn->error);
        }

        $stmt->bind_param("i", $admin_id);

        if (!$stmt->execute()) {
            die("Error in getAdminSurveys(): " . $stmt->error);
        }

        $result = $stmt->get_result();
        $surveys = array();

        while ($row = $result->fetch_assoc()) {
            $surveys[] = $row;
        }

        return $surveys;
    }
}



function saveSurvey($admin_id, $title, $description, $conn)
{
    $sql = "INSERT INTO surveys (admin_id, title, description) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error in saveSurvey(): " . $conn->error);
    }

    $stmt->bind_param("iss", $admin_id, $title, $description);
    if (!$stmt->execute()) {
        die("Error in saveSurvey(): " . $stmt->error);
    }

    return $stmt->insert_id;
}



function saveQuestion($survey_id, $text, $conn)
{
    $sql = "INSERT INTO questions (survey_id, text) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error in saveQuestion(): " . $conn->error);
    }

    $stmt->bind_param("is", $survey_id, $text);
    if (!$stmt->execute()) {
        die("Error in saveQuestion(): " . $stmt->error);
    }

    return $stmt->insert_id;
}

function saveAnswer($question_id, $text, $conn)
{
    $sql = "INSERT INTO answers (question_id, text) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error in saveAnswer(): " . $conn->error);
    }

    $stmt->bind_param("is", $question_id, $text);
    if (!$stmt->execute()) {
        die("Error in saveAnswer(): " . $stmt->error);
    }
}

function getAdminSurveys($admin_id, $conn)
{
    $sql = "SELECT * FROM surveys WHERE admin_id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error in getAdminSurveys(): " . $conn->error);
    }

    $stmt->bind_param("i", $admin_id);
    if (!$stmt->execute()) {
        die("Error in getAdminSurveys(): " . $stmt->error);
    }

    $result = $stmt->get_result();
    $surveys = array();

    while ($row = $result->fetch_assoc()) {
        $surveys[] = $row;
    }

    return $surveys;
}

function getQuestionsForSurvey($survey_id, $conn)
{
    $sql = "SELECT * FROM questions WHERE survey_id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error in getQuestionsForSurvey(): " . $conn->error);
    }

    $stmt->bind_param("i", $survey_id);
    if (!$stmt->execute()) {
        die("Error in getQuestionsForSurvey(): " . $stmt->error);
    }

    $result = $stmt->get_result();
    $questions = array();

    while ($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }

    return $questions;
}

function getAnswersForQuestion($question_id, $conn)
{
    $sql = "SELECT * FROM answers WHERE question_id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error in getAnswersForQuestion(): " . $conn->error);
    }

    $stmt->bind_param("i", $question_id);
    if (!$stmt->execute()) {
        die("Error in getAnswersForQuestion(): " . $stmt->error);
    }

    $result = $stmt->get_result();
    $answers = array();

    while ($row = $result->fetch_assoc()) {
        $answers[] = $row;
    }

    return $answers;
}


function deleteSurvey($survey_id, $conn) {
    // Удаление опроса по его ID
    $stmt = $conn->prepare("DELETE FROM surveys WHERE id = ?");
    $stmt->bind_param("i", $survey_id);
    $stmt->execute();
    $stmt->close();

    // Удаление связанных вопросов и ответов
    deleteQuestionsAndAnswers($survey_id, $conn);
}

function deleteQuestionsAndAnswers($survey_id, $conn) {
    // Получение всех вопросов опроса
    $questions = getQuestionsForSurvey($survey_id, $conn);

    // Удаление ответов для каждого вопроса
    foreach ($questions as $question) {
        deleteAnswersForQuestion($question['id'], $conn);
    }

    // Удаление вопросов опроса
    $stmt = $conn->prepare("DELETE FROM questions WHERE survey_id = ?");
    $stmt->bind_param("i", $survey_id);
    $stmt->execute();
    $stmt->close();
}

function deleteAnswersForQuestion($question_id, $conn) {
    // Удаление ответов для конкретного вопроса
    $stmt = $conn->prepare("DELETE FROM answers WHERE question_id = ?");
    $stmt->bind_param("i", $question_id);
    $stmt->execute();
    $stmt->close();
}

function getSurveyById($survey_id, $conn) {
    // Получение информации об опросе по его ID
    $stmt = $conn->prepare("SELECT * FROM surveys WHERE id = ?");
    $stmt->bind_param("i", $survey_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $survey = $result->fetch_assoc();
    $stmt->close();

    return $survey;
}

function editSurvey($survey_id, $title, $description, $questions, $answers, $conn) {
    // Редактирование опроса
    $stmt = $conn->prepare("UPDATE surveys SET title = ?, description = ? WHERE id = ?");
    $stmt->bind_param("ssi", $title, $description, $survey_id);
    $stmt->execute();
    $stmt->close();

    // Удаление существующих вопросов и ответов для данного опроса
    deleteQuestionsAndAnswers($survey_id, $conn);

    // Добавление отредактированных вопросов и ответов
    foreach ($questions as $index => $questionText) {
        $question_id = saveQuestion($survey_id, $questionText, $conn);

        // Проверка наличия ответов для данного вопроса
        if (isset($answers[$index]["text"])) {
            foreach ($answers[$index]["text"] as $answerText) {
                saveAnswer($question_id, $answerText, $conn);
            }
        }
    }
}

function updateSurvey($survey_id, $title, $description, $conn) {
    $stmt = $conn->prepare("UPDATE surveys SET title = ?, description = ? WHERE id = ?");
    $stmt->bind_param("ssi", $title, $description, $survey_id);
    $stmt->execute();
    $stmt->close();
}

// Дополнительные функции для обновления вопросов и ответов, если они не созданы ранее
// ...

// Пример функции для обновления вопроса
function updateQuestion($question_id, $text, $conn) {
    $stmt = $conn->prepare("UPDATE questions SET text = ? WHERE id = ?");
    $stmt->bind_param("si", $text, $question_id);
    $stmt->execute();
    $stmt->close();
}

// Пример функции для обновления ответа
function updateAnswer($answer_id, $text, $conn) {
    $stmt = $conn->prepare("UPDATE answers SET text = ? WHERE id = ?");
    $stmt->bind_param("si", $text, $answer_id);
    $stmt->execute();
    $stmt->close();
}


function saveUserResponse($user_id, $survey_id, $question_id, $answer_text, $conn) {
    // Ваш код для сохранения ответа в базе данных
    // Например, выполнение SQL-запроса
    $sql = "INSERT INTO user_responses (user_id, survey_id, question_id, answer_text) 
            VALUES ($user_id, $survey_id, $question_id, '$answer_text')";
    mysqli_query($conn, $sql);
}

function getAllSurveys($conn) {
    $surveys = array();

    $sql = "SELECT * FROM surveys";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $surveys[] = $row;
        }
        mysqli_free_result($result);
    }

    return $surveys;
}
?>

