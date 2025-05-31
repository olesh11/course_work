<?php
require_once 'connection.php';
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'registered') {
    die("Доступ заборонено.");
}

$connection = getConnection('registered');

$adspace_id = $_GET['id'] ?? null;

if (!$adspace_id) {
    die("Не вказано ID послуги.");
}

// Оновлення
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $ad_type = $_POST['ad_type'];
    $ad_description = $_POST['ad_description'];
    $ad_price = $_POST['ad_price'];
    $photo = $_POST['photo'];

    $sql = "UPDATE Adspace SET ad_type=?, ad_description=?, ad_price=?, photo=? WHERE adspace_id=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("ssdsi", $ad_type, $ad_description, $ad_price, $photo, $adspace_id);

    if ($stmt->execute()) {
        header("Location: services.php");
        exit;
    } else {
        echo "Помилка оновлення: " . $stmt->error;
    }
}

// Видалення
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $sql = "DELETE FROM Adspace WHERE adspace_id=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $adspace_id);

    if ($stmt->execute()) {
        header("Location: services.php");
        exit;
    } else {
        echo "Помилка видалення: " . $stmt->error;
    }
}

// Отримати дані послуги
$stmt = $connection->prepare("SELECT * FROM Adspace WHERE adspace_id=?");
$stmt->bind_param("i", $adspace_id);
$stmt->execute();
$result = $stmt->get_result();
$adspace = $result->fetch_assoc();

if (!$adspace) {
    die("Послугу не знайдено.");
}
?>

<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8" />
    <title>Редагування послуги</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0 20px 60px;
            /* Місце для footer */
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
            max-width: 500px;
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
        form input[type="number"],
        form textarea {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 20px;
            border: 1.8px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
            resize: vertical;
        }

        form input[type="text"]:focus,
        form input[type="number"]:focus,
        form textarea:focus {
            border-color: #007acc;
            outline: none;
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
            <h1>Редагування послуги</h1>
            <form method="post">
                <label for="ad_type">Тип реклами:</label>
                <input id="ad_type" type="text" name="ad_type" value="<?= htmlspecialchars($adspace['ad_type']) ?>" required />

                <label for="ad_description">Опис реклами:</label>
                <textarea id="ad_description" name="ad_description" rows="4" required><?= htmlspecialchars($adspace['ad_description']) ?></textarea>

                <label for="ad_price">Ціна (грн/міс):</label>
                <input id="ad_price" type="number" name="ad_price" step="0.01" min="0" value="<?= htmlspecialchars($adspace['ad_price']) ?>" required />

                <label for="photo">Фото (URL):</label>
                <input id="photo" type="text" name="photo" value="<?= htmlspecialchars($adspace['photo']) ?>" />

                <div class="buttons">
                    <button type="submit" name="update">Оновити</button>
                    <button type="submit" name="delete" onclick="return confirm('Ви впевнені, що хочете видалити цю послугу?')">Видалити</button>
                    <a href="services.php" class="back">Назад</a>
                </div>
            </form>
        </div>
    </main>

    <?php include 'footer.html'; ?>

</body>

</html>