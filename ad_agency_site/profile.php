<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}

$connection = getConnection('registered');
$user_id = $_SESSION['user_id'];
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['update_profile'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);

    $check_sql = "SELECT user_id FROM Users WHERE user_email = ? AND user_id != ?";
    $stmt = mysqli_prepare($connection, $check_sql);
    mysqli_stmt_bind_param($stmt, "si", $email, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
      $error = "Цей email вже використовується іншим користувачем.";
    } else if ($first_name === '' || $last_name === '' || $email === '') {
        $error = "Поля не можуть бути порожніми.";
    } else {
      $update_sql = "UPDATE Users SET first_name = ?, last_name = ?, user_email = ? WHERE user_id = ?";
      $stmt = mysqli_prepare($connection, $update_sql);
      mysqli_stmt_bind_param($stmt, "sssi", $first_name, $last_name, $email, $user_id);

      if (mysqli_stmt_execute($stmt)) {
        $success = "Дані успішно оновлені.";
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;
        $_SESSION['user_email'] = $email;
      } else {
        $error = "Помилка оновлення: " . mysqli_error($connection);
      }
    }
    mysqli_stmt_close($stmt);
  }

  if (isset($_POST['delete_account'])) {
    $delete_sql = "DELETE FROM Users WHERE user_id = ?";
    $stmt = mysqli_prepare($connection, $delete_sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);

    if (mysqli_stmt_execute($stmt)) {
      mysqli_stmt_close($stmt);
      mysqli_close($connection);
      session_destroy();
      header('Location: register.php');
      exit();
    } else {
      $error = "Помилка видалення акаунта: " . mysqli_error($connection);
    }
  }
}

$user_sql = "SELECT first_name, last_name, user_email FROM Users WHERE user_id = ?";
$stmt = mysqli_prepare($connection, $user_sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($user = mysqli_fetch_assoc($result)) {
  $first_name = $user['first_name'];
  $last_name = $user['last_name'];
  $email = $user['user_email'];
} else {
  header('Location: login.php');
  exit();
}
mysqli_stmt_close($stmt);

$reviews = [];
$review_sql = "SELECT review_id, review_content FROM Reviews WHERE user_id = ?";
$stmt = mysqli_prepare($connection, $review_sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
  $reviews[] = $row;
}
mysqli_stmt_close($stmt);

$bookings = [];
$booking_sql = "
  SELECT b.booking_id, b.booking_date, b.message, a.ad_type 
  FROM Bookings b
  JOIN Adspace a ON b.adspace_id = a.adspace_id
  WHERE b.user_id = ?
  ORDER BY b.booking_date DESC
";
$stmt = mysqli_prepare($connection, $booking_sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
  $bookings[] = $row;
}
mysqli_stmt_close($stmt);

mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="uk">

<head>
  <meta charset="UTF-8" />
  <title>Профіль користувача</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f5f7fa;
      margin: 0;
      padding: 40px 20px;
      color: #333;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      min-height: 100vh;
      flex-direction: column;
    }

    .container {
      background-color: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      padding: 30px 40px;
      width: 100%;
      max-width: 480px;
      box-sizing: border-box;
      margin: 0 auto;
    }

    h2 {
      text-align: center;
      color: #007acc;
      margin-bottom: 30px;
    }

    form {
      margin-bottom: 25px;
    }

    label {
      display: block;
      font-weight: 600;
      margin-top: 15px;
      color: #555;
    }

    input[type="text"],
    input[type="email"] {
      width: 100%;
      padding: 10px 12px;
      margin-top: 6px;
      border: 1.5px solid #ccc;
      border-radius: 6px;
      font-size: 15px;
      box-sizing: border-box;
      transition: border-color 0.3s ease;
    }

    input[type="text"]:focus,
    input[type="email"]:focus {
      border-color: #007acc;
      outline: none;
    }

    button {
      margin-top: 25px;
      padding: 12px 0;
      font-size: 16px;
      font-weight: 600;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      width: 100%;
      transition: background-color 0.3s ease;
    }

    button[name="update_profile"] {
      background-color: #007acc;
      color: white;
    }

    button[name="update_profile"]:hover {
      background-color: #005f99;
    }

    .btn-delete {
      background-color: #e74c3c;
      color: white;
    }

    .btn-delete:hover {
      background-color: #c0392b;
    }

    .btn-logout {
      background-color: #27ae60;
      color: white;
    }

    .btn-logout:hover {
      background-color: #1e8449;
    }

    .success,
    .error {
      text-align: center;
      font-weight: 600;
      margin-bottom: 20px;
    }

    .success {
      color: #27ae60;
    }

    .error {
      color: #e74c3c;
    }

    .btn-back {
      display: inline-block;
      margin-top: 15px;
      width: 100%;
      padding: 12px 0;
      font-size: 16px;
      font-weight: 600;
      text-align: center;
      border: none;
      border-radius: 8px;
      background-color: #95a5a6;
      color: white;
      text-decoration: none;
      transition: background-color 0.3s ease;
    }

    .btn-back:hover {
      background-color: #7f8c8d;
    }

    .reviews-container,
    .bookings-container {
      max-width: 800px;
      margin: 30px auto 80px;
      padding: 0 10px;
    }

    .review-card,
    .booking-card {
      background-color: white;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      padding: 20px;
      margin-bottom: 20px;
      box-sizing: border-box;
      transition: transform 0.3s ease;
      width: 100%;
      max-width: 900px;
      cursor: pointer;
      color: inherit;
      text-decoration: none;
      display: block;
    }

    .review-card:hover,
    .booking-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
      text-decoration: none;
      color: inherit;
    }

    .review-content,
    .booking-content {
      font-size: 16px;
      color: #333;
      white-space: pre-wrap;
    }
  </style>
</head>

<body>
  <div class="container">
    <h2>Профіль користувача</h2>

    <?php if ($success): ?>
      <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" action="profile.php">
      <label for="first_name">Ім'я:</label>
      <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($first_name) ?>" required />

      <label for="last_name">Прізвище:</label>
      <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($last_name) ?>" required />

      <label for="email">Електронна пошта:</label>
      <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required />

      <button type="submit" name="update_profile">Зберегти зміни</button>
    </form>

    <form method="post" action="profile.php" onsubmit="return confirm('Ви впевнені, що хочете видалити акаунт? Цю дію неможливо скасувати.');">
      <button type="submit" name="delete_account" class="btn-delete">Видалити акаунт</button>
    </form>

    <form method="post" action="logout.php">
      <button type="submit" class="btn-logout">Вийти з акаунта</button>
    </form>

    <a href="index.php" class="btn-back">Назад</a>
  </div>

  <h3 style="max-width: 480px; margin: 30px auto 15px;">Ваші відгуки</h3>
  <?php if (empty($reviews)): ?>
    <p style="max-width: 480px; margin: 0 auto 80px;">Ви ще не залишали відгуків.</p>
  <?php else: ?>
    <div class="reviews-container">
      <?php foreach ($reviews as $review): ?>
        <a href="edit_review.php?id=<?= $review['review_id'] ?>" class="review-card">
          <p class="review-content"><?= htmlspecialchars($review['review_content']) ?></p>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <h3 style="max-width: 480px; margin: 30px auto 15px;">Ваші бронювання</h3>
  <?php if (empty($bookings)): ?>
    <p style="max-width: 480px; margin: 0 auto 80px;">Ви ще не робили бронювань.</p>
  <?php else: ?>
    <div class="bookings-container">
      <?php foreach ($bookings as $booking): ?>
        <a href="edit_booking.php?id=<?= urlencode($booking['booking_id']) ?>" class="booking-card-link" style="text-decoration: none; color: inherit;">
          <div class="booking-card">
            <p><strong>Дата бронювання:</strong> <?= htmlspecialchars($booking['booking_date']) ?></p>
            <p><strong>Повідомлення:</strong> <?= nl2br(htmlspecialchars($booking['message'])) ?></p>
            <p><strong>Тип реклами:</strong> <?= htmlspecialchars($booking['ad_type']) ?></p>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</body>

</html>