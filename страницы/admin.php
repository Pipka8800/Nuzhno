<?php 
session_start();

// подключение к бд
$db = new PDO('mysql:host=localhost;dbname=demka', 'root', null,
    [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
);

// Проверка авторизации
if(!isset($_SESSION['token']) || empty($_SESSION['token'])) {
    header('Location: login.php');
    exit();
}

// Проверка прав администратора
$token = $_SESSION['token'];
$user = $db->query("SELECT id, type FROM users WHERE token = '$token'")->fetchAll();

if(empty($user) || $user[0]['type'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Обработка выхода из аккаунта
if(isset($_POST['logout'])) {
    // Очищаем токен в БД
    $stmt = $db->prepare("UPDATE users SET token = NULL WHERE token = ?");
    $stmt->execute([$token]);
    
    // Очищаем сессию
    session_destroy();
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Таблица пользователей</title>
</head>
<body>
    <div class="container">
        <form method="post" class="logout-button">
            <button type="submit" name="logout">Выйти из аккаунта</button>
        </form>
        
        <button class="add-button">+Добавить</button>
        
        <table>
            <tr>
                <th>Пользователи</th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <td>Пользователь 1</td>
                <td><button class="action-button">Ред.</button></td>
                <td><button class="action-button">Удал.</button></td>
            </tr>
            <tr>
                <td>Пользователь 2</td>
                <td><button class="action-button">Ред.</button></td>
                <td><button class="action-button">Удал.</button></td>
            </tr>
            <tr>
                <td>Пользователь 3</td>
                <td><button class="action-button">Ред.</button></td>
                <td><button class="action-button">Удал.</button></td>
            </tr>
            <tr>
                <td>Пользователь 4</td>
                <td><button class="action-button">Ред.</button></td>
                <td><button class="action-button">Удал.</button></td>
            </tr>
            <tr>
                <td>Пользователь 5</td>
                <td><button class="action-button">Ред.</button></td>
                <td><button class="action-button">Удал.</button></td>
            </tr>
        </table>
    </div>
</body>
</html>