<?php
require_once 'connection.php';
session_start();

$connection = getConnection($_SESSION['user_role'] ?? 'guest');
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['client_name'] ?? '');
    $person = trim($_POST['contact_person'] ?? '');
    $email = trim($_POST['client_email'] ?? '');
    $phone = trim($_POST['client_phone'] ?? '');
    $photo_path = '';

    if ($name === '' || $person === '' || $email === '' || $phone === '') {
        $error_message = "Всі поля, окрім фото, мають бути заповнені.";
    } else {
        if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $tmp_name = $_FILES['photo']['tmp_name'];
            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid('client_', true) . '.' . $ext;
            $target_path = $upload_dir . $new_filename;

            if (move_uploaded_file($tmp_name, $target_path)) {
                $photo_path = $target_path;
            } else {
                $error_message = "Не вдалося завантажити файл.";
            }
        }

        if ($error_message === '') {
            $sql = "INSERT INTO Clients (client_name, contact_person, client_email, client_phone, photo)
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $connection->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("sssss", $name, $person, $email, $phone, $photo_path);
                if ($stmt->execute()) {
                    header('Location: clients.php');
                    exit;
                } else {
                    $error_message = "Помилка виконання запиту: " . $stmt->error;
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
    <title>Додати клієнта</title>
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
            max-width: 480px;
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
        input[type="email"],
        input[type="file"] {
            width: 100%;
            padding: 10px 12px;
            font-size: 14px;
            border: 1.8px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }

        input:focus {
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
            text-align: center;
        }
    </style>
</head>
<body>
    <?php include 'header.html'; ?>

    <main>
        <form method="post" enctype="multipart/form-data" novalidate>
            <h1>Додати нового клієнта</h1>

            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>

            <label>Назва клієнта:
                <input type="text" name="client_name" required value="<?= htmlspecialchars($_POST['client_name'] ?? '') ?>">
            </label>

            <label>Контактна особа:
                <input type="text" name="contact_person" required value="<?= htmlspecialchars($_POST['contact_person'] ?? '') ?>">
            </label>

            <label>Email:
                <input type="email" name="client_email" required value="<?= htmlspecialchars($_POST['client_email'] ?? '') ?>">
            </label>

            <label>Телефон:
                <input type="text" name="client_phone" required value="<?= htmlspecialchars($_POST['client_phone'] ?? '') ?>">
            </label>

            <label>Фото:
                <input type="file" name="photo" accept="image/*">
            </label>

            <button type="submit">Додати</button>
            <a href="clients.php" class="back-btn">Повернутися назад</a>
        </form>
    </main>

    <?php include 'footer.html'; ?>
</body>
</html>
