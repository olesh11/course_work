<!DOCTYPE html>
<html lang="uk">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Course Agency</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #fff;
    }

    header {
      display: flex;
      align-items: center;
      padding: 20px 60px;
      background-color: white;
      border-bottom: 1px solid #ccc;
      justify-content: space-between;
    }

    .logo {
      font-weight: bold;
      font-size: 22px;
      flex: 1;
    }

    nav {
      flex: 2;
      display: flex;
      justify-content: center;
      gap: 30px;
    }

    nav a {
      text-decoration: none;
      color: black;
      font-size: 18px;
    }

    .register-btn {
      flex: 1;
      display: flex;
      justify-content: flex-end;
    }

    .register-btn button {
      padding: 10px 20px;
      background-color: #0095e9;
      color: white;
      border: none;
      border-radius: 4px;
      font-size: 14px;
      cursor: pointer;
    }


    .main {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 60px;
      background-color: #f7f1ee;
      border-radius: 20px;
      margin: 40px 60px;
    }

    .text-block {
      max-width: 50%;
    }

    .text-block h1 {
      font-size: 22px;
      font-weight: normal;
      margin-bottom: 10px;
    }

    .text-block p {
      font-size: 24px;
      font-weight: bold;
      line-height: 1.5;
      margin-bottom: 30px;
    }

    .btn {
      padding: 10px 20px;
      background-color: #0095e9;
      color: white;
      border: none;
      border-radius: 4px;
      font-size: 14px;
      cursor: pointer;
      text-decoration: none;
    }

    .main img {
      max-width: 45%;
      height: auto;
    }

    .stats {
      display: flex;
      justify-content: center;
      gap: 60px;
      margin: 40px 60px;
      text-align: center;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .stat-item {
      max-width: 200px;
    }

    .stat-number {
      font-size: 36px;
      font-weight: bold;
      color: #0095e9;
      margin-bottom: 10px;
    }

    .stat-text {
      font-size: 16px;
      color: #555;
      line-height: 1.3;
    }

    footer {
      background-color: #f0f0f0;
      padding: 40px 60px 20px 60px;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #444;
      margin-top: 40px;
      border-top: 1px solid #ccc;
    }

    .footer-container {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 30px;
      max-width: 1200px;
      margin: 0 auto;
    }

    .footer-section {
      flex: 1 1 250px;
      min-width: 220px;
    }

    .footer-section h3 {
      color: #007acc;
      margin-bottom: 15px;
      font-weight: bold;
    }

    .footer-section p,
    .footer-section a {
      font-size: 14px;
      line-height: 1.6;
      color: #555;
      text-decoration: none;
    }

    .footer-section a:hover {
      color: #0095e9;
      text-decoration: underline;
    }

    .footer-bottom {
      text-align: center;
      margin-top: 30px;
      font-size: 13px;
      color: #888;
    }

    .campaigns-promo {
      text-align: center;
      margin: 40px 60px;
    }

    .campaigns-promo h2 {
      font-weight: normal;
      font-size: 20px;
      margin-bottom: 50px;
      margin-top: 50px;
      color: #333;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .campaigns-promo .btn {
      font-size: 16px;
      padding: 12px 24px;
      background-color: #0095e9;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      text-decoration: none;
      display: inline-block;
      transition: background-color 0.3s ease;
    }

    .campaigns-promo .btn:hover {
      background-color: #007acc;
    }
  </style>
</head>

<?php
session_start();

if (!empty($_SESSION['first_name'])) {
  $firstName = htmlspecialchars($_SESSION['first_name']);
} else {
  $firstName = "Користувач";
}
?>

<body>
  <?php if (!empty($_SESSION['success_message'])): ?>
    <div id="welcomePopup" style="
    position: fixed;
    top: 20px;
    right: 20px;
    background: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
    padding: 15px 20px;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    z-index: 1000;
    max-width: 300px;
    font-family: Arial, sans-serif;
">
      <span style="font-weight: bold;">Привіт, <?= $firstName ?>! Ви успішно увійшли в систему.</span>
      <button onclick="document.getElementById('welcomePopup').style.display='none';" style="
        float: right;
        background: transparent;
        border: none;
        font-weight: bold;
        font-size: 16px;
        cursor: pointer;
        color: #155724;
        line-height: 1;
    ">&times;</button>
      <div style="clear: both;"></div>
    </div>

    <script>
      setTimeout(function() {
        var popup = document.getElementById('welcomePopup');
        if (popup) popup.style.display = 'none';
      }, 5000);
    </script>

    <?php unset($_SESSION['success_message']); ?>
  <?php endif; ?>


  <header>
    <div class="logo">Course Agency</div>
    <nav>
      <a href="clients.php">Клієнти</a>
      <a href="services.php">Послуги</a>
      <a href="about-us.php">Про нас</a>
      <a href="reviews.php">Відгуки</a>
      <a href="contacts.php">Контакти</a>
    </nav>
    <div class="register-btn">
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="profile.php" class="btn">Профіль</a>
      <?php else: ?>
        <a href="register.php" class="btn">Реєстрація</a>
      <?php endif; ?>
    </div>
  </header>

  <div class="main">
    <div class="text-block">
      <h1>Course Agency</h1>
      <p>Розміщення зовнішньої реклами по всій Україні.<br />
        Проведемо для вас ефективну рекламну кампанію, що працює на результат.</p>
      <a href="add_booking.php" class="btn">Зробити онлайн бронювання</a>
    </div>
    <img src="images/billboards.jpg" alt="Billboards">
  </div>
  <div class="stats">
    <div class="stat-item">
      <div class="stat-number">1230</div>
      <div class="stat-text">рекламно-інформаційних площин</div>
    </div>
    <div class="stat-item">
      <div class="stat-number">27</div>
      <div class="stat-text">населених пунктів з нашими носіями</div>
    </div>
    <div class="stat-item">
      <div class="stat-number">700</div>
      <div class="stat-text">постійно задоволених клієнтів</div>
    </div>
  </div>
  <div class="campaigns-promo">
    <h2>Перегляньте найновіші рекламні кампанії з нашими партнерами</h2>
    <a href="campaigns.php" class="btn">Переглянути кампанії</a>
  </div>

  <footer>
    <div class="footer-container">
      <div class="footer-section about">
        <h3>Про Course Agency</h3>
        <p>Ми допомагаємо бізнесам по всій Україні розміщувати ефективну зовнішню рекламу та просуватися в онлайн. Наша
          мета — зробити ваш бренд помітним і успішним.</p>
      </div>
      <div class="footer-section contacts">
        <h3>Контакти</h3>
        <p>Телефон: +38 096 123 4567</p>
        <p>Email: romanolesh@gmail.com</p>
        <p>Адреса: вул. Князя Романа, 1, Львів, Україна</p>
      </div>
      <div class="footer-section social">
        <h3>Ми в соцмережах</h3>
        <p><a href="https://www.facebook.com/profile.php?id=100017613705719&locale=uk_UA">Facebook</a></p>
        <p><a href="https://www.instagram.com/roman_olesh/">Instagram</a></p>
        <p><a href="https://www.linkedin.com/in/%D1%80%D0%BE%D0%BC%D0%B0%D0%BD-%D0%BE%D0%BB%D0%B5%D1%88-7b344a323/">LinkedIn</a></p>
      </div>
    </div>
  </footer>


</body>

</html>