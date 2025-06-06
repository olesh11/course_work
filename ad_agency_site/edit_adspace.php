<?php
require_once 'connection.php';
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'registered') {
    die("Доступ заборонено.");
}

$connection = getConnection('registered');

$adspace_id = $_GET['id'] ?? null;
$stmt = $connection->prepare("SELECT * FROM Adspace WHERE adspace_id=?");
$stmt->bind_param("i", $adspace_id);
$stmt->execute();
$result = $stmt->get_result();
$adspace = $result->fetch_assoc();

if (!$adspace_id) {
    die("Не вказано ID послуги.");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['delete'])) {
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

    if (isset($_POST['update'])) {
        $ad_type = trim($_POST['ad_type'] ?? '');
        $ad_description = trim($_POST['ad_description'] ?? '');
        $ad_price = trim($_POST['ad_price'] ?? '');

        if ($ad_type === '' || $ad_description === '' || $ad_price === '') {
            $error = "Будь ласка, заповніть усі обов’язкові поля.";
        } elseif (!is_numeric($ad_price) || floatval($ad_price) < 0) {
            $error = "Ціна повинна бути додатнім числом.";
        } else {
            $photo = $adspace['photo'];

            if (isset($_FILES['photo_file']) && $_FILES['photo_file']['error'] !== UPLOAD_ERR_NO_FILE) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $fileTmpPath = $_FILES['photo_file']['tmp_name'];
                $fileType = mime_content_type($fileTmpPath);

                if (!in_array($fileType, $allowedTypes)) {
                    $error = "Неприпустимий формат файлу. Дозволені: JPG, PNG, GIF.";
                } else {
                    $uploadDir = __DIR__ . '/uploads/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $fileName = basename($_FILES['photo_file']['name']);
                    $fileName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $fileName);
                    $newFilePath = $uploadDir . time() . '_' . $fileName;

                    if (move_uploaded_file($fileTmpPath, $newFilePath)) {
                        $photo = 'uploads/' . basename($newFilePath);
                    } else {
                        $error = "Помилка завантаження файлу.";
                    }
                }
            }
        }

        if ($error === '') {
            $sql = "UPDATE Adspace SET ad_type=?, ad_description=?, ad_price=?, photo=? WHERE adspace_id=?";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("ssdsi", $ad_type, $ad_description, $ad_price, $photo, $adspace_id);

            if ($stmt->execute()) {
                header("Location: services.php");
                exit;
            } else {
                $error = "Помилка оновлення: " . $stmt->error;
            }
        }
    }
}

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

        .error-message {
            color: red;
            font-weight: 600;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>

<body>

    <?php include 'header.html'; ?>

    <main>
        <div class="edit-container">
            <h1>Редагування послуги</h1>

            <?php if ($error): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <label for="ad_type">Тип реклами:</label>
                <input id="ad_type" type="text" name="ad_type" value="<?= htmlspecialchars($_POST['ad_type'] ?? $adspace['ad_type']) ?>" required />

                <label for="ad_description">Опис реклами:</label>
                <textarea id="ad_description" name="ad_description" rows="4" required><?= htmlspecialchars($_POST['ad_description'] ?? $adspace['ad_description']) ?></textarea>

                <label for="ad_price">Ціна (грн/міс):</label>
                <input id="ad_price" type="number" name="ad_price" step="0.01" min="0" value="<?= htmlspecialchars($_POST['ad_price'] ?? $adspace['ad_price']) ?>" required />

                <label for="photo_file">Фото (файл):</label>
                <input id="photo_file" type="file" name="photo_file" accept="image/*" />

                <?php if (!empty($adspace['photo'])): ?>
                    <p>Поточне фото:</p>
                    <img src="<?= htmlspecialchars($adspace['photo']) ?>" alt="Фото послуги" style="max-width: 100%; height: auto; margin-bottom: 20px; border-radius: 8px;">
                <?php endif; ?>

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