<?php session_start(); 

// 1. проверка наличия токена : локально ($_SESSION['token']) и сравнение с бд
// 2. есди есть --> перекидываем на страницу пользователя/админ
// 3. если нету --> остаёмся на этой странице

// подключение к бд
$db = new PDO('mysql:host=localhost;dbname=demka', 'root', null,
    [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
);

// Проверка существующей авторизации
if(isset($_SESSION['token']) && !empty($_SESSION['token'])){
    $token = $_SESSION['token'];
    // запрос на получение пользователя по токену
    $user = $db->query("SELECT id, type FROM users WHERE token = '$token'")->fetchAll();

    // если пользователь есть
    if(!empty($user)){
        $userType = $user[0]['type'];
        $isAdmin = $userType == 'admin';
        $isUser = $userType == 'user';

        // перенаправление на страницу пользователя/админ
        if($isAdmin) {
            header('Location: admin.php');
            exit();
        } elseif($isUser) {
            header('Location: user.php');
            exit();
        }
    }
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    // 1. Получаем данные из $_POST
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // 2. Проверяем заполнены ли поля
    $errors = [];
    if(empty($login)) {
        $errors['login'] = 'Необходимо заполнить';
    }
    if(empty($password)) {
        $errors['password'] = 'Необходимо заполнить';
    }
    
    // Если ошибок нет, проверяем данные в БД
    if(empty($errors)) {
        // 3. Сравниваем значения с БД
        $stmt = $db->prepare("SELECT id, type, password FROM users WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch();
        
        if($user && $password === $user['password']) { // В реальном проекте используйте password_verify()
            // Генерируем новый токен
            $token = bin2hex(random_bytes(32));
            
            // Сохраняем токен в БД
            $stmt = $db->prepare("UPDATE users SET token = ? WHERE id = ?");
            $stmt->execute([$token, $user['id']]);
            
            // Сохраняем токен в сессии
            $_SESSION['token'] = $token;
            
            // Редиректим на нужную страницу
            if($user['type'] === 'admin') {
                header('Location: admin.php');
            } else {
                header('Location: user.php');
            }
            exit();
        } else {
            $errors['auth'] = 'Неверный логин/пароль';
        }
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Авторизация</title>
</head>
    <div class="login">
        <form action="login.php" method="post">
            <h1 class="login-title">Авторизация</h1>
            <label for="login">
                Введите логин
                <?php if(isset($errors['login'])): ?>
                    <span class="error"><?php echo $errors['login']; ?></span>
                <?php endif; ?>
            </label>
            <input type="text" name="login" id="login" value="<?php echo htmlspecialchars($login ?? ''); ?>">
            <label for="password">
                Введите пароль
                <?php if(isset($errors['password'])): ?>
                    <span class="error"><?php echo $errors['password']; ?></span>
                <?php endif; ?>
            </label>
            <input type="password" name="password" id="password">
            <button type="submit">Вход</button>
            <?php if(isset($errors['auth'])): ?>
                <p class="error"><?php echo $errors['auth']; ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>