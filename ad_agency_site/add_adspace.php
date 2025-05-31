<?php
require_once 'connection.php';
session_start();

$connection = getConnection($_SESSION['user_role'] ?? 'guest');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ad_type = $_POST['ad_type'] ?? '';
    $ad_description = $_POST['ad_description'] ?? '';
    $ad_price = $_POST['ad_price'] ?? 0;
    $photo = $_POST['photo'] ?? '';

    $sql = "INSERT INTO Adspace (ad_type, ad_description, ad_price, photo)
            VALUES (?, ?, ?, ?)";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("ssds", $ad_type, $ad_description, $ad_price, $photo);

    if ($stmt->execute()) {
        header('Location: services.php');
        exit;
    } else {
        echo "Помилка: " . $stmt->error;
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
            /* Залишаємо місце під footer */
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

        @media (max-width: 500px) {
            body {
                padding: 0 10px 60px;
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
            <h1>Додати рекламне місце</h1>
            <label>Тип реклами:
                <input type="text" name="ad_type" required>
            </label>
            <label>Опис реклами:
                <textarea name="ad_description" rows="4" required></textarea>
            </label>
            <label>Ціна:
                <input type="number" step="0.01" name="ad_price" required>
            </label>
            <label>Фото (URL або шлях):
                <input type="text" name="photo">
            </label>
            <button type="submit">Додати</button>

            <a href="services.php" class="back-btn">Повернутися назад</a>
        </form>
    </main>

    <?php include 'footer.html'; ?>

</body>

</html>