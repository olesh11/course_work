<?php
require_once 'connection.php';
session_start();

$connection = getConnection($_SESSION['user_role'] ?? 'guest');
$error_message = '';

$clients = [];
$resultClients = $connection->query("SELECT client_id, client_name FROM Clients ORDER BY client_name");
if ($resultClients) {
    while ($row = $resultClients->fetch_assoc()) {
        $clients[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_name = trim($_POST['client_name'] ?? '');
    $campaign_name = trim($_POST['campaign_name'] ?? '');
    $campaign_description = trim($_POST['campaign_description'] ?? '');
    $photo_path = '';

    if ($client_name === '' && $campaign_name === '' && $campaign_description === '' && empty($_FILES['photo']['name'])) {
        $error_message = "Усі поля не можуть бути порожніми або заповненими лише пробілами.";
    } else {
        // Обробка завантаження фото
        if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $tmp_name = $_FILES['photo']['tmp_name'];
            $filename = basename($_FILES['photo']['name']);
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $new_filename = uniqid('photo_', true) . '.' . $ext;
            $target_path = $upload_dir . $new_filename;

            if (move_uploaded_file($tmp_name, $target_path)) {
                $photo_path = $target_path;
            } else {
                $error_message = "Не вдалося завантажити файл.";
            }
        }

        if (empty($error_message)) {
            $stmtClient = $connection->prepare("SELECT client_id FROM Clients WHERE client_name = ?");
            if ($stmtClient) {
                $stmtClient->bind_param("s", $client_name);
                $stmtClient->execute();
                $stmtClient->bind_result($client_id);

                if ($stmtClient->fetch()) {
                    $stmtClient->close();

                    $stmtInsert = $connection->prepare("INSERT INTO Campaigns (client_id, campaign_name, campaign_description, photo) VALUES (?, ?, ?, ?)");
                    if ($stmtInsert) {
                        $stmtInsert->bind_param("isss", $client_id, $campaign_name, $campaign_description, $photo_path);
                        if ($stmtInsert->execute()) {
                            header('Location: campaigns.php');
                            exit;
                        } else {
                            $error_message = "Помилка при додаванні кампанії: " . $stmtInsert->error;
                        }
                        $stmtInsert->close();
                    } else {
                        $error_message = "Помилка підготовки запиту вставки: " . $connection->error;
                    }
                } else {
                    $error_message = "Клієнта з таким ім'ям не знайдено.";
                    $stmtClient->close();
                }
            } else {
                $error_message = "Помилка підготовки запиту пошуку клієнта: " . $connection->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8" />
    <title>Додати кампанію</title>
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
            max-width: 500px;
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
        textarea,
        select,
        input[type="file"] {
            width: 100%;
            padding: 10px 12px;
            font-size: 14px;
            border: 1.8px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
            resize: vertical;
        }

        input:focus,
        textarea:focus,
        select:focus {
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
            <h1>Додати нову рекламну кампанію</h1>

            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>

            <label>Виберіть клієнта:
                <select name="client_name" required>
                    <option value="" disabled <?= !isset($_POST['client_name']) ? 'selected' : '' ?>>Оберіть клієнта</option>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?= htmlspecialchars($client['client_name']) ?>"
                            <?= (isset($_POST['client_name']) && $_POST['client_name'] === $client['client_name']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($client['client_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>Назва кампанії:
                <input type="text" name="campaign_name" required value="<?= htmlspecialchars($_POST['campaign_name'] ?? '') ?>">
            </label>

            <label>Опис кампанії:
                <textarea name="campaign_description" rows="5"><?= htmlspecialchars($_POST['campaign_description'] ?? '') ?></textarea>
            </label>

            <label>Фото:
                <input type="file" name="photo" accept="image/*">
            </label>

            <button type="submit">Додати</button>
            <a href="campaigns.php" class="back-btn">Повернутися назад</a>
        </form>
    </main>

    <?php include 'footer.html'; ?>
</body>

</html>