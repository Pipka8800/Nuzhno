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
$user = $db->query("SELECT id, type, name, surname FROM users WHERE token = '$token'")->fetchAll();

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

$errors = [];
$editUserId = null;
$editUser = null;

// Обработка добавления пользователя
if(isset($_POST['add_user'])) {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';
    $name = $_POST['name'] ?? '';
    $surname = $_POST['surname'] ?? '';
    $type = $_POST['type'] ?? 'user';

    // Валидация
    if(empty($login)) $errors['login'] = 'Необходимо заполнить';
    if(empty($password)) $errors['password'] = 'Необходимо заполнить';
    if(empty($name)) $errors['name'] = 'Необходимо заполнить';
    if(empty($surname)) $errors['surname'] = 'Необходимо заполнить';

    // Проверка существования логина
    if(empty($errors)) {
        $stmt = $db->prepare("SELECT id FROM users WHERE login = ?");
        $stmt->execute([$login]);
        if($stmt->fetch()) {
            $errors['login'] = 'Такой логин уже существует';
        }
    }

    // Добавление пользователя
    if(empty($errors)) {
        $stmt = $db->prepare("INSERT INTO users (login, password, name, surname, type) VALUES (?, ?, ?, ?, ?)");
        if($stmt->execute([$login, $password, $name, $surname, $type])) {
            header('Location: admin.php');
            exit();
        }
    }
}

// Получение данных для редактирования
if(isset($_POST['user_id'])) {
    $editUserId = $_POST['user_id'];
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$editUserId]);
    $editUser = $stmt->fetch();
}

// Обработка редактирования
if(isset($_POST['edit_user'])) {
    $editUserId = $_POST['user_id'];
    $name = $_POST['name'] ?? '';
    $surname = $_POST['surname'] ?? '';
    $password = $_POST['password'] ?? '';

    // Валидация
    if(empty($name)) $errors['edit_name'] = 'Необходимо заполнить';
    if(empty($surname)) $errors['edit_surname'] = 'Необходимо заполнить';

    if(empty($errors)) {
        if(!empty($password)) {
            // Если указан новый пароль
            $stmt = $db->prepare("UPDATE users SET name = ?, surname = ?, password = ? WHERE id = ?");
            $result = $stmt->execute([$name, $surname, $password, $editUserId]);
        } else {
            // Если пароль не меняется
            $stmt = $db->prepare("UPDATE users SET name = ?, surname = ? WHERE id = ?");
            $result = $stmt->execute([$name, $surname, $editUserId]);
        }

        if($result) {
            header('Location: admin.php');
            exit();
        }
    }
}

// Получаем список всех пользователей
$users = $db->query("SELECT id, login, name, surname, type, blocked, amountAttempt, latest FROM users ORDER BY id")->fetchAll();

// Обработка блокировки/разблокировки
if(isset($_POST['toggle_block']) && isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];
    $newStatus = $_POST['current_status'] == '1' ? 0 : 1;
    
    $stmt = $db->prepare("UPDATE users SET blocked = ? WHERE id = ?");
    $stmt->execute([$newStatus, $userId]);
    
    header('Location: admin.php');
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
        <?php if(!empty($user)): ?>
            <p style="text-align: center; margin-bottom: 20px;">
                <?php 
                echo htmlspecialchars($user[0]['name'] . ' ' . $user[0]['surname']);
                echo ' / ';
                echo $user[0]['type'] === 'admin' ? 'Администратор' : 'Пользователь';
                ?>
            </p>
        <?php endif; ?>
        
        <div class="action-buttons">
            <button class="add-button" onclick="document.getElementById('addForm').style.display = 'block'; document.getElementById('editForm').style.display = 'none';">Добавить пользователя</button>
            <button class="edit-button" onclick="document.getElementById('editForm').style.display = 'block'; document.getElementById('addForm').style.display = 'none';">Редактировать</button>
        </div>

        <!-- Форма добавления пользователя -->
        <div id="addForm" style="display: none;" class="form-container">
            <h2>Добавить пользователя</h2>
            <form method="post" action="">
                <div class="form-group">
                    <label>Логин:
                        <?php if(isset($errors['login'])): ?>
                            <span class="error"><?php echo $errors['login']; ?></span>
                        <?php endif; ?>
                    </label>
                    <input type="text" name="login" value="<?php echo $_POST['login'] ?? ''; ?>">
                </div>

                <div class="form-group">
                    <label>Пароль:
                        <?php if(isset($errors['password'])): ?>
                            <span class="error"><?php echo $errors['password']; ?></span>
                        <?php endif; ?>
                    </label>
                    <input type="password" name="password">
                </div>

                <div class="form-group">
                    <label>Имя:
                        <?php if(isset($errors['name'])): ?>
                            <span class="error"><?php echo $errors['name']; ?></span>
                        <?php endif; ?>
                    </label>
                    <input type="text" name="name" value="<?php echo $_POST['name'] ?? ''; ?>">
                </div>

                <div class="form-group">
                    <label>Фамилия:
                        <?php if(isset($errors['surname'])): ?>
                            <span class="error"><?php echo $errors['surname']; ?></span>
                        <?php endif; ?>
                    </label>
                    <input type="text" name="surname" value="<?php echo $_POST['surname'] ?? ''; ?>">
                </div>

                <div class="form-group">
                    <label>Тип пользователя:</label>
                    <select name="type">
                        <option value="user">Пользователь</option>
                        <option value="admin">Администратор</option>
                    </select>
                </div>

                <button type="submit" name="add_user" class="submit-button">Добавить</button>
            </form>
        </div>

        <!-- Форма редактирования -->
        <div id="editForm" style="display: none;" class="form-container">
            <h2>Редактировать пользователя</h2>
            <form method="post" action="">
                <div class="form-group">
                    <label>Выберите пользователя:</label>
                    <select name="user_id" onchange="this.form.submit()">
                        <option value="">Выберите пользователя</option>
                        <?php foreach($users as $u): ?>
                            <option value="<?php echo $u['id']; ?>" <?php echo ($editUserId == $u['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($u['login'] . ' (' . $u['name'] . ' ' . $u['surname'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if($editUser): ?>
                    <div class="form-group">
                        <label>Имя:
                            <?php if(isset($errors['edit_name'])): ?>
                                <span class="error"><?php echo $errors['edit_name']; ?></span>
                            <?php endif; ?>
                        </label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($editUser['name']); ?>">
                    </div>

                    <div class="form-group">
                        <label>Фамилия:
                            <?php if(isset($errors['edit_surname'])): ?>
                                <span class="error"><?php echo $errors['edit_surname']; ?></span>
                            <?php endif; ?>
                        </label>
                        <input type="text" name="surname" value="<?php echo htmlspecialchars($editUser['surname']); ?>">
                    </div>

                    <div class="form-group">
                        <label>Новый пароль (оставьте пустым, чтобы не менять):</label>
                        <input type="password" name="password">
                    </div>

                    <input type="hidden" name="user_id" value="<?php echo $editUser['id']; ?>">
                    <button type="submit" name="edit_user" class="submit-button">Сохранить изменения</button>
                <?php endif; ?>
            </form>
        </div>
        
        <table>
            <tr>
                <th>Логин</th>
                <th>Имя</th>
                <th>Фамилия</th>
                <th>Статус</th>
                <th>Попыток входа</th>
                <th>Последняя активность</th>
                <th>Действия</th>
            </tr>
            <?php foreach($users as $u): ?>
                <tr>
                    <td><?php echo htmlspecialchars($u['login']); ?></td>
                    <td><?php echo htmlspecialchars($u['name']); ?></td>
                    <td><?php echo htmlspecialchars($u['surname']); ?></td>
                    <td><?php echo $u['blocked'] ? 'Заблокирован' : 'Активен'; ?></td>
                    <td><?php echo $u['amountAttempt']; ?></td>
                    <td><?php echo $u['latest'] ? date('d.m.Y H:i', strtotime($u['latest'])) : 'Нет данных'; ?></td>
                    <td>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                            <input type="hidden" name="current_status" value="<?php echo $u['blocked']; ?>">
                            <button type="submit" name="toggle_block" class="<?php echo $u['blocked'] ? 'unblock-button' : 'block-button'; ?>">
                                <?php echo $u['blocked'] ? 'Разблокировать' : 'Заблокировать'; ?>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>