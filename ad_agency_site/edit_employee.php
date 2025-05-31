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

// Оновлення
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $position = $_POST['position'];
    $email = $_POST['employee_email'];
    $photo = $_POST['photo'];

    $sql = "UPDATE Employees SET first_name=?, last_name=?, position=?, employee_email=?, photo=? WHERE employee_id=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("sssssi", $first_name, $last_name, $position, $email, $photo, $employee_id);

    if ($stmt->execute()) {
        header("Location: contacts.php");
        exit;
    } else {
        echo "Помилка оновлення: " . $stmt->error;
    }
}

// Видалення
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $sql = "DELETE FROM Employees WHERE employee_id=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $employee_id);

    if ($stmt->execute()) {
        header("Location: contacts.php");
        exit;
    } else {
        echo "Помилка видалення: " . $stmt->error;
    }
}

// Отримати дані співробітника
$stmt = $connection->prepare("SELECT * FROM Employees WHERE employee_id=?");
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();

if (!$employee) {
    die("Співробітника не знайдено.");
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
        form input[type="email"] {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 20px;
            border: 1.8px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }

        form input[type="text"]:focus,
        form input[type="email"]:focus {
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
        }

        .back:hover {
            background-color: #005fa3;
        }

        @media (max-width: 600px) {
            .edit-container {
                padding: 20px;
            }
        }
    </style>
</head>

<body>

    <?php include 'header.html'; ?>

    <main>
        <div class="edit-container">
            <h1>Редагування співробітника</h1>
            <form method="post">
                <label for="first_name">Ім'я:</label>
                <input id="first_name" type="text" name="first_name" value="<?= htmlspecialchars($employee['first_name']) ?>" required>

                <label for="last_name">Прізвище:</label>
                <input id="last_name" type="text" name="last_name" value="<?= htmlspecialchars($employee['last_name']) ?>" required>

                <label for="position">Посада:</label>
                <input id="position" type="text" name="position" value="<?= htmlspecialchars($employee['position']) ?>" required>

                <label for="employee_email">Email:</label>
                <input id="employee_email" type="email" name="employee_email" value="<?= htmlspecialchars($employee['employee_email']) ?>" required>

                <label for="photo">Фото (URL):</label>
                <input id="photo" type="text" name="photo" value="<?= htmlspecialchars($employee['photo']) ?>">

                <div class="buttons">
                    <button type="submit" name="update">Оновити</button>
                    <button type="submit" name="delete" onclick="return confirm('Ви впевнені, що хочете видалити цього співробітника?')">Видалити</button>
                    <a href="contacts.php" class="back">Назад</a>
                </div>
            </form>
        </div>
    </main>

    <?php include 'footer.html'; ?>

</body>

</html>