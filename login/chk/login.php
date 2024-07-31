<?php
session_start();

// Подключение к базе данных
$mysqli = new mysqli("localhost", "root", "", "cli_ar");


if ($mysqli->connect_error) {
    die("Ошибка подключения: ");
}

// Получение данных из формы
$email = trim(filter_var( $_POST['login'], FILTER_SANITIZE_SPECIAL_CHARS));
$password = trim(filter_var(  $_POST['pass'], FILTER_SANITIZE_SPECIAL_CHARS));

// Поиск пользователя в базе данных
$stmt = $mysqli->prepare("SELECT id, pass FROM users WHERE login = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($user_id, $pass);

if ($stmt->num_rows > 0) {
    $stmt->fetch();
    if ($password == $pass) {
        $_SESSION['user_id'] = $user_id;
        header("Location: ./home_pg/home.php");
        exit();
    } else {
        echo 'Неверный пароль.';
    }
} else {
    echo 'Пользователь не найден.';
}

$stmt->close();
$mysqli->close();
?>