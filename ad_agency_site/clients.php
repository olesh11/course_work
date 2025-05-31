<?php
require_once 'connection.php';
session_start();
$role = $_SESSION['user_role'] ?? 'guest';
$connection = getConnection($role);

$searchTerm = $_GET['search'] ?? '';
$sql = "SELECT * FROM Clients";
if (!empty($searchTerm)) {
    $searchTerm = "%" . $connection->real_escape_string($searchTerm) . "%";
    $sql .= " WHERE client_name LIKE '$searchTerm'";
}
$result = mysqli_query($connection, $sql);
if (!$result) {
    die("Помилка запиту: " . mysqli_error($connection));
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8" />
  <title>Клієнти</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f5f7fa;
      margin: 0;
      padding: 20px;
      color: #333;
    }
    h1 {
      text-align: center;
      color: #007acc;
      margin-bottom: 15px;
    }
    .intro-text {
      max-width: 700px;
      margin: 0 auto 40px auto;
      font-size: 16px;
      line-height: 1.6;
      text-align: center;
      color: #444;
    }
    .controls {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin-bottom: 20px;
    }
    .controls form,
    .controls a {
      display: inline-block;
    }
    .clients-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
      max-width: 1200px;
      margin: 0 auto;
    }
    .client-card {
      background-color: white;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      width: 280px;
      padding: 20px;
      box-sizing: border-box;
      display: flex;
      flex-direction: column;
      align-items: center;
      transition: transform 0.3s ease;
      cursor: pointer;
      text-decoration: none;
      color: inherit;
    }
    .client-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    .client-photo {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 15px;
      border: 2px solid #0095e9;
    }
    .client-name {
      font-weight: bold;
      font-size: 18px;
      color: #007acc;
      margin-bottom: 8px;
      text-align: center;
    }
    .client-contact {
      font-size: 14px;
      color: #555;
      margin-bottom: 6px;
      text-align: center;
    }
    .client-email, .client-phone {
      font-size: 14px;
      color: #333;
      word-break: break-all;
      text-align: center;
      margin-bottom: 6px;
    }
    .no-photo {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      background-color: #ddd;
      color: #777;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
      margin-bottom: 15px;
      user-select: none;
    }
    .search-input {
      padding: 5px;
      font-size: 14px;
    }
    .search-btn, .add-btn {
      padding: 5px 10px;
      font-size: 14px;
      cursor: pointer;
      border: none;
      border-radius: 5px;
      background-color: #007acc;
      color: white;
    }
    @media (max-width: 600px) {
      .client-card {
        width: 90%;
      }
    }
  </style>
</head>
<body>

<?php include 'header.html'; ?>

<h1>Наші клієнти</h1>

<div class="intro-text">
  Course Agency — це ваш надійний партнер у світі зовнішньої реклами. Ми допомагаємо бізнесам по всій Україні ефективно розміщувати рекламні кампанії, що приносять результат.
</div>

<div class="controls">
  <form method="get">
    <input class="search-input" type="text" name="search" placeholder="Пошук за назвою" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
    <button class="search-btn" type="submit">Пошук</button>
  </form>
  <?php if ($role !== 'guest'): ?>
    <a href="add_client.php" class="add-btn">Додати</a>
  <?php endif; ?>
</div>

<div class="clients-container">
  <?php if (mysqli_num_rows($result) > 0): ?>
    <?php while ($client = mysqli_fetch_assoc($result)): ?>
      <?php $cardTag = ($role === 'registered') ? 'a href="edit_client.php?id=' . $client['client_id'] . '"' : 'div'; ?>
      <<?= $cardTag ?> class="client-card">
        <?php if ($client['photo']): ?>
          <img class="client-photo" src="<?= htmlspecialchars($client['photo']) ?>" alt="<?= htmlspecialchars($client['client_name']) ?>">
        <?php else: ?>
          <div class="no-photo">Фото відсутнє</div>
        <?php endif; ?>
        <div class="client-name"><?= htmlspecialchars($client['client_name']) ?></div>
        <div class="client-contact">Контактна особа: <?= htmlspecialchars($client['contact_person']) ?></div>
        <div class="client-email">Email: <?= htmlspecialchars($client['client_email']) ?></div>
        <div class="client-phone">Телефон: <?= htmlspecialchars($client['client_phone']) ?></div>
      </<?= explode(' ', $cardTag)[0] ?>>
    <?php endwhile; ?>
  <?php else: ?>
    <p style="text-align:center; color:#666;">Клієнтів не знайдено.</p>
  <?php endif; ?>
</div>

<?php include 'footer.html'; ?>

</body>
</html>
