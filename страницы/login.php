<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Авторизация</title>
</head>
    <div class="login">
        <form>
            <h1 class="login-title">Авторизация</h1>
            <label for="login">
                Введите логин
                <span class="error">Необходимо заполнить</span>
            </label>
            <input type="text" name="login" id="login">
            <label for="password">
                Введите пароль
                <span class="error">Необходимо заполнить</span>
            </label>
            <input type="password" name="password" id="password">
            <button type="submit">Вход</button>
            <p class="error">Неверный логин/пароль</p>
        </form>
    </div>
</body>
</html>