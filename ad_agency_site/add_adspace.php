<?php
require_once 'connection.php';
session_start();

$connection = getConnection($_SESSION['user_role'] ?? 'guest');
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ad_type = trim($_POST['ad_type'] ?? '');
    $ad_description = trim($_POST['ad_description'] ?? '');
    $ad_price = floatval($_POST['ad_price'] ?? 0);
    $photo_path = '';

    if ($ad_type === '' || $ad_description === '') {
        $error_message = "Тип реклами та опис не можуть бути порожніми.";
    } elseif ($ad_price <= 0) {
        $error_message = "Ціна повинна бути більшою за нуль.";
    } else {
        $upload_dir = 'uploads/';
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['photo']['tmp_name'];
            $original_name = basename($_FILES['photo']['name']);
            $target_file = $upload_dir . uniqid() . '_' . $original_name;

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            if (move_uploaded_file($tmp_name, $target_file)) {
                $photo_path = $target_file;
            } else {
                $error_message = "Не вдалося завантажити файл.";
            }
        }

        if (empty($error_message)) {
            $sql = "INSERT INTO Adspace (ad_type, ad_description, ad_price, photo)
                    VALUES (?, ?, ?, ?)";
            $stmt = $connection->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("ssds", $ad_type, $ad_description, $ad_price, $photo_path);
                if ($stmt->execute()) {
                    header('Location: services.php');
                    exit;
                } else {
                    $error_message = "Помилка: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error_message = "Помилка підготовки запиту: " . $connection->error;
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title>Додати рекламне місце</title>
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

        form {
            background-color: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            max-width: 450px;
            width: 100%;
            box-sizing: border-box;
        }

        h1 {
            text-align: center;
            color: #007acc;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 18px;
            font-size: 15px;
            color: #444;
        }

        input[type="text"],
        input[type="number"],
        input[type="file"],
        textarea {
            width: 100%;
            padding: 10px 12px;
            font-size: 14px;
            border: 1.8px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
            resize: vertical;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="file"]:focus,
        textarea:focus {
            border-color: #007acc;
            outline: none;
        }

        button {
            background-color: #007acc;
            color: white;
            padding: 12px 18px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            width: 100%;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        button:hover {
            background-color: #005fa3;
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

        .error-message {
            background-color: #ffe5e5;
            color: #cc0000;
            padding: 10px 14px;
            border: 1.5px solid #ff9999;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>

<body>

    <?php include 'header.html'; ?>

    <main>
        <form method="post" enctype="multipart/form-data" novalidate>
            <h1>Додати рекламне місце</h1>
            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            <label>Тип реклами:
                <input type="text" name="ad_type" required>
            </label>
            <label>Опис реклами:
                <textarea name="ad_description" rows="4" required></textarea>
            </label>
            <label>Ціна:
                <input type="number" step="0.01" name="ad_price" required>
            </label>
            <label>Фото:
                <input type="file" name="photo" accept="image/*">
            </label>
            <button type="submit">Додати</button>

            <a href="services.php" class="back-btn">Повернутися назад</a>
        </form>
    </main>

    <?php include 'footer.html'; ?>

</body>

</html>