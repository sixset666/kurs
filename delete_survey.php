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
    deleteSurvey($survey_id, $conn);

    // Можно добавить дополнительные действия после успешного удаления опроса
    header("Location: admin_profile.php");
    exit();
}
?>
