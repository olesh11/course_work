<?php
require_once 'connection.php';
session_start();
$role = $_SESSION['user_role'] ?? 'guest';
$connection = getConnection($role);

$user_id = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_content']) && $user_id !== null) {
    $review_content = trim($_POST['review_content']);
    if ($review_content !== '') {
        $review_content_escaped = $connection->real_escape_string($review_content);
        $user_id_int = (int)$user_id;
        $insert_sql = "INSERT INTO Reviews (user_id, review_content) VALUES ($user_id_int, '$review_content_escaped')";
        if (!mysqli_query($connection, $insert_sql)) {
            $error_message = "Помилка додавання відгуку: " . mysqli_error($connection);
        } else {
            header("Location: reviews.php");
            exit();
        }
    } else {
        $error_message = "Відгук не може бути порожнім.";
    }
}

$searchTerm = trim($_GET['search'] ?? '');

$sql = "SELECT r.review_id, r.review_content, u.first_name, u.last_name 
        FROM Reviews r
        JOIN Users u ON r.user_id = u.user_id";

if (!empty($searchTerm)) {
    $searchTerm = "%" . $connection->real_escape_string($searchTerm) . "%";
    $sql .= " WHERE u.first_name LIKE '$searchTerm' OR u.last_name LIKE '$searchTerm' OR r.review_content LIKE '$searchTerm'";
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
    <title>Відгуки</title>
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

        .reviews-container {
            max-width: 900px;
            margin: 0 auto 40px auto;
        }

        .review-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            box-sizing: border-box;
            transition: transform 0.3s ease;
        }

        .review-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .review-author {
            font-weight: bold;
            font-size: 18px;
            color: #007acc;
            margin-bottom: 8px;
        }

        .review-content {
            font-size: 16px;
            color: #333;
            white-space: pre-wrap;
        }

        .search-input {
            padding: 5px;
            font-size: 14px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 250px;
        }

        .search-btn {
            padding: 5px 10px;
            font-size: 14px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            background-color: #007acc;
            color: white;
        }

        .add-review-form {
            max-width: 700px;
            margin: 0 auto 40px auto;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .add-review-form textarea {
            width: 100%;
            height: 100px;
            font-size: 16px;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            resize: vertical;
            box-sizing: border-box;
            margin-bottom: 10px;
        }

        .add-review-form button {
            padding: 10px 15px;
            font-size: 16px;
            background-color: #007acc;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .error-message {
            color: red;
            margin-bottom: 10px;
            text-align: center;
        }

        .login-message {
            text-align: center;
            margin-bottom: 30px;
            color: #666;
        }
    </style>
</head>

<body>

    <?php include 'header.html'; ?>

    <h1>Відгуки наших користувачів</h1>

    <div class="intro-text">
        Тут ви можете прочитати відгуки наших користувачів про наші послуги та досвід співпраці.
    </div>

    <div class="controls">
        <form method="get">
            <input class="search-input" type="text" name="search" placeholder="Пошук за ім'ям, прізвищем або текстом відгуку" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <button class="search-btn" type="submit">Пошук</button>
        </form>
    </div>

    <?php if ($user_id !== null): ?>
        <div class="add-review-form">
            <form method="post" action="reviews.php">
                <?php if (!empty($error_message)): ?>
                    <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
                <?php endif; ?>
                <textarea name="review_content" placeholder="Напишіть свій відгук тут..." required></textarea>
                <button type="submit">Додати відгук</button>
            </form>
        </div>
    <?php else: ?>
        <p class="login-message">Щоб додати відгук, будь ласка, <a href="login.php">увійдіть у свій акаунт</a>.</p>
    <?php endif; ?>

    <h1>Всі відгуки</h1>

    <div class="reviews-container">
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($review = mysqli_fetch_assoc($result)) {
                echo '<div class="review-card">';
                echo '<div class="review-author">' . htmlspecialchars($review['first_name']) . ' ' . htmlspecialchars($review['last_name']) . '</div>';
                echo '<div class="review-content">' . htmlspecialchars($review['review_content']) . '</div>';
                echo '</div>';
            }
        } else {
            echo '<p style="text-align:center; color:#666;">Відгуків не знайдено.</p>';
        }
        mysqli_close($connection);
        ?>
    </div>

    <?php include 'footer.html'; ?>

</body>

</html>