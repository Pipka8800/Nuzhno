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
            <h1 class="login-title">Сброс пароля</h1>
            <label for="password">
                Введите пароль
                <span class="error">Необходимо заполнить</span>
            </label>
            <input type="password" name="password" id="password">
            <label for="password">
                Павторите пароль
                <span class="error">Необходимо заполнить</span>
            </label>
            <input type="password" name="password" id="password">
            <button type="submit">Изменить пароль</button>
            <p class="error">Пароли не совпадают</p>
        </form>
    </div>
</body>
</html>