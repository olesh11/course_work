<?php
require_once 'connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$connection = getConnection($_SESSION['user_role'] ?? 'guest');
$user_id = (int)$_SESSION['user_id'];
$error_message = "";

// Отримуємо список доступних AdSpace
$adspace_result = $connection->query("SELECT adspace_id, ad_type FROM AdSpace");
$adspaces = [];
if ($adspace_result) {
    while ($row = $adspace_result->fetch_assoc()) {
        $adspaces[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adspace_id = (int)($_POST['adspace_id'] ?? 0);
    $message = trim($_POST['message'] ?? '');

    if ($adspace_id === 0) {
        $error_message = "Будь ласка, оберіть рекламне місце.";
    } else {
        $sql = "INSERT INTO Bookings (user_id, adspace_id, message) VALUES (?, ?, ?)";
        $stmt = $connection->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("iis", $user_id, $adspace_id, $message);
            if ($stmt->execute()) {
                header('Location: profile.php');
                exit();
            } else {
                $error_message = "Помилка додавання бронювання: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_message = "Помилка підготовки запиту: " . $connection->error;
        }
    }
}

$connection->close();
?>

<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title>Додати бронювання</title>
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

        select,
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

        select:focus,
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

        p.error-message {
            color: red;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
        }

        @media (max-width: 500px) {
            main {
                padding: 20px 10px;
            }

            form {
                padding: 20px;
            }
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
            text-align: center;
        }

        .back-btn:hover {
            background-color: #c0c0c0;
        }
    </style>
</head>

<body>

    <?php include 'header.html'; ?>

    <main>
        <form method="post" novalidate>
            <h1>Додати бронювання</h1>

            <?php if ($error_message): ?>
                <p class="error-message"><?= htmlspecialchars($error_message) ?></p>
            <?php endif; ?>

            <label for="adspace_id">Оберіть рекламне місце:
                <select name="adspace_id" id="adspace_id" required>
                    <option value="">-- Виберіть --</option>
                    <?php foreach ($adspaces as $adspace): ?>
                        <option value="<?= $adspace['adspace_id'] ?>">
                            <?= htmlspecialchars($adspace['ad_type']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label for="message">Повідомлення (необов’язково):
                <textarea id="message" name="message" rows="4" placeholder="Ваше повідомлення..."></textarea>
            </label>

            <button type="submit">Забронювати</button>

            <a href="index.php" class="back-btn">Повернутися назад</a>
        </form>
    </main>

    <?php include 'footer.html'; ?>

</body>

</html>