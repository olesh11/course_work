<?php
session_start();
require_once 'connection.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $connection = getConnection('guest'); // гостьове підключення для входу

    $query = "SELECT * FROM Users WHERE user_email = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['user_email'] = $row['user_email'];
            $_SESSION['user_role'] = 'registered';
            $_SESSION['first_name'] = $row['first_name'];
            $_SESSION['last_name'] = $row['last_name'];

            $_SESSION['success_message'] = "Ви успішно увійшли в систему!";

            header("Location: index.php");
            exit();
        } else {
            $error = "Невірний пароль.";
        }
    } else {
        $error = "Користувача з таким email не знайдено.";
    }

    mysqli_close($connection);
}
?>

<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title>Вхід</title>
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
    <h2>Вхід</h2>

    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" action="login.php">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Пароль:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Увійти</button>
    </form>

    <p class="link">Не маєте акаунту? <a href="register.php">Зареєструватися</a></p>

    <a href="index.php" class="back-btn">Повернутися на головну</a>
  </div>

</body>

</html>
