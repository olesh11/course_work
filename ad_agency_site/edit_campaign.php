<?php
require_once 'connection.php';
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'registered') {
    die("Доступ заборонено.");
}

$connection = getConnection('registered');

$campaign_id = $_GET['id'] ?? null;

if (!$campaign_id) {
    die("Не вказано ID кампанії.");
}

// Оновлення
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $client_id = $_POST['client_id'];
    $name = $_POST['campaign_name'];
    $description = $_POST['campaign_description'];
    $photo = $_POST['photo'];

    $sql = "UPDATE Campaigns SET client_id=?, campaign_name=?, campaign_description=?, photo=? WHERE campaign_id=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("isssi", $client_id, $name, $description, $photo, $campaign_id);

    if ($stmt->execute()) {
        header("Location: campaigns.php");
        exit;
    } else {
        echo "Помилка оновлення: " . $stmt->error;
    }
}

// Видалення
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $sql = "DELETE FROM Campaigns WHERE campaign_id=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $campaign_id);

    if ($stmt->execute()) {
        header("Location: campaigns.php");
        exit;
    } else {
        echo "Помилка видалення: " . $stmt->error;
    }
}

// Отримати дані кампанії
$stmt = $connection->prepare("SELECT * FROM Campaigns WHERE campaign_id=?");
$stmt->bind_param("i", $campaign_id);
$stmt->execute();
$result = $stmt->get_result();
$campaign = $result->fetch_assoc();

if (!$campaign) {
    die("Кампанію не знайдено.");
}

// Для випадаючого списку клієнтів
$clients_result = $connection->query("SELECT client_id, client_name FROM Clients ORDER BY client_name");
$clients = [];
while ($row = $clients_result->fetch_assoc()) {
    $clients[] = $row;
}
?>

<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8" />
    <title>Редагування кампанії</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0 20px 60px;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 40px 0;
        }

        .edit-container {
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
            box-sizing: border-box;
        }

        h1 {
            text-align: center;
            color: #007acc;
            margin-bottom: 30px;
        }

        form label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #444;
        }

        form input[type="text"],
        form textarea,
        form select {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 20px;
            border: 1.8px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
            font-family: inherit;
        }

        form input[type="text"]:focus,
        form textarea:focus,
        form select:focus {
            border-color: #007acc;
            outline: none;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        .buttons {
            display: flex;
            gap: 10px;
        }

        button,
        .back {
            flex: 1;
            padding: 12px 0;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            user-select: none;
            text-align: center;
            text-decoration: none;
            color: white;
            transition: background-color 0.3s ease;
            border: none;
            display: inline-flex;
            justify-content: center;
            align-items: center;
        }

        button[name="update"] {
            background-color: #007acc;
        }

        button[name="update"]:hover {
            background-color: #005fa3;
        }

        button[name="delete"] {
            background-color: #d9534f;
        }

        button[name="delete"]:hover {
            background-color: #b52b27;
        }

        .back {
            background-color: #007acc;
            line-height: 1;
            text-decoration: none;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .back:hover {
            background-color: #005fa3;
        }

        @media (max-width: 600px) {
            .edit-container {
                padding: 20px 20px;
            }

            button {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>

    <?php include 'header.html'; ?>

    <main>
        <div class="edit-container">
            <h1>Редагування кампанії</h1>
            <form method="post">
                <label for="client_id">Клієнт:</label>
                <select id="client_id" name="client_id" required>
                    <option value="">-- Виберіть клієнта --</option>
                    <?php foreach ($clients as $client_option): ?>
                        <option value="<?= $client_option['client_id'] ?>" <?= ($client_option['client_id'] == $campaign['client_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($client_option['client_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="campaign_name">Назва кампанії:</label>
                <input id="campaign_name" type="text" name="campaign_name" value="<?= htmlspecialchars($campaign['campaign_name']) ?>" required>

                <label for="campaign_description">Опис кампанії:</label>
                <textarea id="campaign_description" name="campaign_description"><?= htmlspecialchars($campaign['campaign_description']) ?></textarea>

                <label for="photo">Фото (URL):</label>
                <input id="photo" type="text" name="photo" value="<?= htmlspecialchars($campaign['photo']) ?>">

                <div class="buttons">
                    <button type="submit" name="update">Оновити</button>
                    <button type="submit" name="delete" onclick="return confirm('Ви впевнені, що хочете видалити цю кампанію?')">Видалити</button>
                    <a href="campaigns.php" class="back">Назад</a>
                </div>
            </form>
        </div>
    </main>

    <?php include 'footer.html'; ?>

</body>

</html>