<?php
require_once 'connection.php';
session_start();

// Перевірка ролі та авторизації
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'registered') {
    die("Доступ заборонено.");
}

$connection = getConnection('registered');

$review_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$review_id) {
    die("Не вказано ID відгуку.");
}

// Перевірка, чи цей відгук належить користувачу
$stmt = $connection->prepare("SELECT review_content FROM Reviews WHERE review_id = ? AND user_id = ?");
$stmt->bind_param("ii", $review_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Відгук не знайдено або доступ заборонено.");
}

$review = $result->fetch_assoc();

// Оновлення відгуку
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $content = trim($_POST['review_content']);
    if ($content === '') {
        $error = "Відгук не може бути порожнім.";
    } else {
        $update_sql = "UPDATE Reviews SET review_content = ? WHERE review_id = ? AND user_id = ?";
        $stmt = $connection->prepare($update_sql);
        $stmt->bind_param("sii", $content, $review_id, $user_id);

        if ($stmt->execute()) {
            header("Location: profile.php");
            exit();
        } else {
            $error = "Помилка оновлення: " . $stmt->error;
        }
    }
}

// Видалення відгуку
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $delete_sql = "DELETE FROM Reviews WHERE review_id = ? AND user_id = ?";
    $stmt = $connection->prepare($delete_sql);
    $stmt->bind_param("ii", $review_id, $user_id);

    if ($stmt->execute()) {
        header("Location: profile.php");
        exit();
    } else {
        $error = "Помилка видалення: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8" />
    <title>Редагування відгуку</title>
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
        }

        .edit-container {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 30px 40px;
            width: 100%;
            max-width: 480px;
            box-sizing: border-box;
        }

        h2 {
            text-align: center;
            color: #007acc;
            margin-bottom: 30px;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #555;
        }

        textarea {
            width: 100%;
            height: 150px;
            padding: 10px 12px;
            border: 1.5px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            resize: vertical;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }

        textarea:focus {
            border-color: #007acc;
            outline: none;
        }

        .buttons {
            margin-top: 25px;
            display: flex;
            gap: 15px;
        }

        button {
            flex: 1;
            padding: 12px 0;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            color: white;
            user-select: none;
        }

        button[name="update"] {
            background-color: #007acc;
        }

        button[name="update"]:hover {
            background-color: #005f99;
        }

        button[name="delete"] {
            background-color: #e74c3c;
        }

        button[name="delete"]:hover {
            background-color: #c0392b;
        }

        .btn-back {
            margin-top: 20px;
            display: inline-block;
            text-decoration: none;
            background-color: #95a5a6;
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .btn-back:hover {
            background-color: #7f8c8d;
        }

        .error {
            margin-top: 15px;
            color: #e74c3c;
            font-weight: 600;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="edit-container">
        <h2>Редагування відгуку</h2>

        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="post">
            <label for="review_content">Відгук:</label>
            <textarea id="review_content" name="review_content" required><?= htmlspecialchars($review['review_content']) ?></textarea>

            <div class="buttons">
                <button type="submit" name="update">Оновити</button>
                <button type="submit" name="delete" onclick="return confirm('Ви впевнені, що хочете видалити цей відгук? Цю дію неможливо скасувати.');">Видалити</button>
            </div>
        </form>

        <a href="profile.php" class="btn-back">Назад до профілю</a>
    </div>
</body>

</html>