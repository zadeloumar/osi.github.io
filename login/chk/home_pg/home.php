<?php
session_start();

// Проверка, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Подключение к базе данных
$mysqli = new mysqli("localhost", "root", "", "cli_ar");

if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}

// Получение данных пользователя
$user_id = $_SESSION['user_id'];
$stmt = $mysqli->prepare("SELECT username, phone FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($firstname);
$stmt->fetch();
$stmt->close();
echo $firstname;
?>
<!DOCTYPE html>
<html lang="en">

</html>