<?php
require_once 'connection.php';
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'registered') {
    die("Доступ заборонено.");
}

$connection = getConnection('registered');
$employee_id = $_GET['id'] ?? null;

if (!$employee_id) {
    die("Не вказано ID співробітника.");
}

$error = '';

$stmt = $connection->prepare("SELECT * FROM Employees WHERE employee_id=?");
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();

if (!$employee) {
    die("Співробітника не знайдено.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['delete'])) {
        $sql = "DELETE FROM Employees WHERE employee_id=?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $employee_id);

        if ($stmt->execute()) {
            header("Location: contacts.php");
            exit;
        } else {
            $error = "Помилка видалення: " . $stmt->error;
        }
    }

    if (isset($_POST['update'])) {
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $position = trim($_POST['position'] ?? '');
        $email = trim($_POST['employee_email'] ?? '');

        if ($first_name === '' || $last_name === '' || $position === '' || $email === '') {
            $error = "Будь ласка, заповніть усі обов’язкові поля.";
        } else {
            $photo = $employee['photo'];

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

            if ($error === '') {
                $sql = "UPDATE Employees SET first_name=?, last_name=?, position=?, employee_email=?, photo=? WHERE employee_id=?";
                $stmt = $connection->prepare($sql);
                $stmt->bind_param("sssssi", $first_name, $last_name, $position, $email, $photo, $employee_id);

                if ($stmt->execute()) {
                    header("Location: contacts.php");
                    exit;
                } else {
                    $error = "Помилка оновлення: " . $stmt->error;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8" />
    <title>Редагування співробітника</title>
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
        form input[type="email"],
        form input[type="file"] {
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
        form input[type="email"]:focus,
        form input[type="file"]:focus {
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

        img.current-photo {
            max-width: 100%;
            height: auto;
            margin-bottom: 20px;
            border-radius: 8px;
            display: block;
        }
    </style>
</head>

<body>

    <?php include 'header.html'; ?>

    <main>
        <div class="edit-container">
            <h1>Редагування співробітника</h1>

            <?php if ($error): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <label for="first_name">Ім'я:</label>
                <input id="first_name" type="text" name="first_name" value="<?= htmlspecialchars($_POST['first_name'] ?? $employee['first_name']) ?>" required>

                <label for="last_name">Прізвище:</label>
                <input id="last_name" type="text" name="last_name" value="<?= htmlspecialchars($_POST['last_name'] ?? $employee['last_name']) ?>" required>

                <label for="position">Посада:</label>
                <input id="position" type="text" name="position" value="<?= htmlspecialchars($_POST['position'] ?? $employee['position']) ?>" required>

                <label for="employee_email">Email:</label>
                <input id="employee_email" type="email" name="employee_email" value="<?= htmlspecialchars($_POST['employee_email'] ?? $employee['employee_email']) ?>" required>

                <label for="photo_file">Фото (файл):</label>
                <input id="photo_file" type="file" name="photo_file" accept="image/*">

                <?php if (!empty($employee['photo'])): ?>
                    <p>Поточне фото:</p>
                    <img class="current-photo" src="<?= htmlspecialchars($employee['photo']) ?>" alt="Поточне фото співробітника" />
                <?php endif; ?>

                <div class="buttons">
                    <button type="submit" name="update">Оновити</button>
                    <button type="submit" name="delete" onclick="return confirm('Ви впевнені, що хочете видалити цього співробітника?');">Видалити</button>
                    <a href="contacts.php" class="back">Назад</a>
                </div>
            </form>
        </div>
    </main>

    <?php include 'footer.html'; ?>

</body>

</html>