<?php
require_once 'connection.php';
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'registered') {
    die("Доступ заборонено.");
}

$connection = getConnection('registered');

$booking_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$booking_id) {
    die("Не вказано ID бронювання.");
}

$error = '';

$stmt = $connection->prepare("SELECT booking_date, message, adspace_id FROM Bookings WHERE booking_id = ? AND user_id = ?");
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Бронювання не знайдено або доступ заборонено.");
}

$booking = $result->fetch_assoc();

$adspace_result = $connection->query("SELECT adspace_id, ad_type FROM Adspace ORDER BY ad_type ASC");
$adspaces = [];
if ($adspace_result) {
    while ($row = $adspace_result->fetch_assoc()) {
        $adspaces[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['delete'])) {
        $delete_sql = "DELETE FROM Bookings WHERE booking_id = ? AND user_id = ?";
        $stmt = $connection->prepare($delete_sql);
        $stmt->bind_param("ii", $booking_id, $user_id);

        if ($stmt->execute()) {
            header("Location: profile.php");
            exit;
        } else {
            $error = "Помилка видалення: " . $stmt->error;
        }
    }

    if (isset($_POST['update'])) {
        $booking_date = trim($_POST['booking_date'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $adspace_id = intval($_POST['adspace_id'] ?? 0);

        if ($booking_date === '' || $adspace_id <= 0 || $message === '') {
            $error = "Будь ласка, заповніть усі обов’язкові поля: дата бронювання та тип реклами.";
        } else {
            $update_sql = "UPDATE Bookings SET booking_date = ?, message = ?, adspace_id = ? WHERE booking_id = ? AND user_id = ?";
            $stmt = $connection->prepare($update_sql);
            $stmt->bind_param("ssiii", $booking_date, $message, $adspace_id, $booking_id, $user_id);

            if ($stmt->execute()) {
                header("Location: profile.php");
                exit;
            } else {
                $error = "Помилка оновлення: " . $stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8" />
    <title>Редагування бронювання</title>
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

        input[type="date"],
        select,
        textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1.5px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }

        textarea {
            height: 120px;
            resize: vertical;
        }

        input[type="date"]:focus,
        select:focus,
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
        <h2>Редагування бронювання</h2>

        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="post">
            <label for="booking_date">Дата бронювання:</label>
            <input type="date" id="booking_date" name="booking_date" required value="<?= htmlspecialchars($_POST['booking_date'] ?? $booking['booking_date']) ?>">

            <label for="message">Повідомлення:</label>
            <textarea id="message" name="message"><?= htmlspecialchars($_POST['message'] ?? $booking['message']) ?></textarea>

            <label for="adspace_id">Тип реклами:</label>
            <select id="adspace_id" name="adspace_id" required>
                <option value="">-- Оберіть тип реклами --</option>
                <?php foreach ($adspaces as $ad): ?>
                    <option value="<?= $ad['adspace_id'] ?>"
                        <?= (isset($_POST['adspace_id']) ? ($_POST['adspace_id'] == $ad['adspace_id']) : ($booking['adspace_id'] == $ad['adspace_id'])) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ad['ad_type']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="buttons">
                <button type="submit" name="update">Оновити</button>
                <button type="submit" name="delete" onclick="return confirm('Ви впевнені, що хочете видалити це бронювання? Цю дію неможливо скасувати.');">Видалити</button>
            </div>
        </form>

        <a href="profile.php" class="btn-back">Назад до профілю</a>
    </div>
</body>

</html>