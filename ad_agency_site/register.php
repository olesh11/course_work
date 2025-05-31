<?php
require_once 'connection.php'; // Підключення до бази (під користувачем guest)

$connection = getConnection('guest');

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check_sql = "SELECT * FROM Users WHERE user_email = ?";
    $check_stmt = mysqli_prepare($connection, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "s", $email);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_num_rows($result) > 0) {
        $error = "Користувач з такою електронною поштою вже існує.";
    } else {
        $insert_sql = "INSERT INTO Users (first_name, last_name, user_email, password) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($connection, $insert_sql);
        mysqli_stmt_bind_param($stmt, "ssss", $first_name, $last_name, $email, $password);

        if (mysqli_stmt_execute($stmt)) {
            $success = "Реєстрація успішна! <a href='login.php'>Увійти</a>";
        } else {
            $error = "Помилка при збереженні: " . mysqli_error($connection);
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_stmt_close($check_stmt);
}

mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Реєстрація</title>
    <style>
      body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f5f7fa;
        margin: 0;
        padding: 40px 20px;
        color: #333;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
      }
      .container {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        padding: 30px 40px;
        width: 100%;
        max-width: 420px;
        box-sizing: border-box;
      }
      h2 {
        text-align: center;
        color: #007acc;
        margin-bottom: 25px;
      }
      form label {
        display: block;
        margin-top: 15px;
        font-weight: 600;
        color: #555;
      }
      form input {
        width: 92%;
        padding: 10px 12px;
        margin-top: 6px;
        border: 1.5px solid #ccc;
        border-radius: 6px;
        font-size: 15px;
        transition: border-color 0.3s ease;
      }
      form input:focus {
        border-color: #007acc;
        outline: none;
      }
      button[type="submit"] {
        margin-top: 25px;
        width: 100%;
        background-color: #007acc;
        color: white;
        font-size: 16px;
        padding: 12px 0;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        font-weight: 600;
      }
      button[type="submit"]:hover {
        background-color: #005f99;
      }
      .message {
        margin-top: 20px;
        text-align: center;
        color: green;
        font-weight: 600;
      }
      .message a {
        color: #007acc;
        text-decoration: none;
      }
      .message a:hover {
        text-decoration: underline;
      }
      .error {
        margin-top: 20px;
        text-align: center;
        color: #cc0000;
        font-weight: 600;
      }
      .link {
        margin-top: 30px;
        text-align: center;
        font-size: 15px;
        color: #555;
      }
      .link a {
        color: #007acc;
        text-decoration: none;
        font-weight: 600;
      }
      .link a:hover {
        text-decoration: underline;
      }
      .back-btn {
        display: block;
        margin: 30px auto 0 auto;
        width: fit-content;
        background-color: #e0e0e0;
        color: #333;
        padding: 8px 20px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: background-color 0.3s ease;
      }
      .back-btn:hover {
        background-color: #c0c0c0;
      }

      @media (max-width: 480px) {
        .container {
          padding: 25px 20px;
          width: 90%;
        }
      }
    </style>
</head>
<body>

  <div class="container">
    <h2>Реєстрація користувача</h2>

    <?php if ($success): ?>
        <p class="message"><?= $success ?></p>
    <?php elseif ($error): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <form action="register.php" method="post">
        <label for="first_name">Ім'я:</label>
        <input type="text" name="first_name" id="first_name" required>

        <label for="last_name">Прізвище:</label>
        <input type="text" name="last_name" id="last_name" required>

        <label for="email">Електронна пошта:</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Пароль:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Зареєструватися</button>
    </form>

    <div class="link">
        Вже маєте акаунт? <a href="login.php">Увійти</a>
    </div>

    <a href="index.php" class="back-btn">Повернутися на головну</a>
  </div>

</body>
</html>
