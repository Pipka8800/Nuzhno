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

// Проверка типа пользователя и обновление времени последнего посещения
$token = $_SESSION['token'];
$user = $db->query("SELECT id, type, name, surname, blocked FROM users WHERE token = '$token'")->fetchAll();

// Обновляем время последнего посещения
$updateLatest = $db->prepare("UPDATE users SET latest = NOW() WHERE token = ?");
$updateLatest->execute([$token]);

if(!empty($user)){
    $userType = $user[0]['type'];
    $isAdmin = $userType == 'admin';
    $isUser = $userType == 'user';
    $isBlocked = $user[0]['blocked'];

    // Если админ, перенаправляем на страницу админа
    if($isAdmin) {
        header('Location: admin.php');
        exit();
    }
} else {
    // Если пользователь не найден, отправляем на страницу входа
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

$errors = [];

if($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['logout'])) {
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    // Валидация
    if(empty($password)) {
        $errors['password'] = 'Необходимо заполнить';
    }
    if(empty($password_confirm)) {
        $errors['password_confirm'] = 'Необходимо заполнить';
    }
    if($password !== $password_confirm) {
        $errors['match'] = 'Пароли не совпадают';
    }
    
    // Если ошибок нет, меняем пароль
    if(empty($errors)) {
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE token = ?");
        if($stmt->execute([$password, $token])) {
            header('Location: login.php');
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Смена пароля</title>
</head>
<body>
    <div class="login">
        <?php if($isBlocked): ?>
            <div class="error-message">
                <h2>Пользователь заблокирован, обратитесь к администрации</h2>
            </div>
        <?php else: ?>
        <form method="post" action="">
            <h1 class="login-title">Сброс пароля</h1>
            <p class="login-p">
                <?php 
                echo htmlspecialchars($user[0]['name'] . ' ' . $user[0]['surname']);
                echo ' / ';
                echo $user[0]['type'] === 'admin' ? 'Администратор' : 'Пользователь';
                ?>
            </p>
            <label for="password">
                Введите пароль
                <?php if(isset($errors['password'])): ?>
                    <span class="error"><?php echo $errors['password']; ?></span>
                <?php endif; ?>
            </label>
            <input type="password" name="password" id="password">
            <label for="password_confirm">
                Повторите пароль
                <?php if(isset($errors['password_confirm'])): ?>
                    <span class="error"><?php echo $errors['password_confirm']; ?></span>
                <?php endif; ?>
            </label>
            <input type="password" name="password_confirm" id="password_confirm">
            <button type="submit">Изменить пароль</button>
            <?php if(isset($errors['match'])): ?>
                <p class="error"><?php echo $errors['match']; ?></p>
            <?php endif; ?>
            <button type="submit" name="logout">Выйти из аккаунта</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>