<?php
require_once 'connection.php';
session_start();
$role = $_SESSION['user_role'] ?? 'guest';
$connection = getConnection($role);

$searchTerm = $_GET['search'] ?? '';
$sql = "SELECT * FROM Employees";
if (!empty($searchTerm)) {
    $searchTerm = "%" . $connection->real_escape_string($searchTerm) . "%";
    $sql .= " WHERE first_name LIKE '$searchTerm' OR last_name LIKE '$searchTerm'";
}
$result = mysqli_query($connection, $sql);
if (!$result) {
    die("Помилка запиту: " . mysqli_error($connection));
}
?>
<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8" />
    <title>Контакти | Наша команда</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 40px;
            color: #333;
        }

        h1 {
            text-align: center;
            color: #007acc;
            margin-bottom: 10px;
        }

        .description {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 40px auto;
            font-size: 16px;
            color: #555;
            line-height: 1.6;
        }

        .controls {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .search-input {
            padding: 8px 10px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .search-btn,
        .add-btn {
            padding: 8px 14px;
            font-size: 15px;
            cursor: pointer;
            border: none;
            border-radius: 6px;
            background-color: #007acc;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .search-btn:hover,
        .add-btn:hover {
            background-color: #005fa3;
        }

        .employees-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            max-width: 1100px;
            margin: 60px auto 150px auto;
        }

        .employee-entry {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            display: flex;
            padding: 20px;
            align-items: center;
            width: calc(50% - 15px);
            box-sizing: border-box;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .employee-entry.clickable:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.12);
        }

        .employee-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #007acc;
            margin-right: 25px;
        }

        .no-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-color: #ddd;
            color: #777;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            margin-right: 25px;
            text-align: center;
        }

        .employee-info {
            display: flex;
            flex-direction: column;
        }

        .employee-name {
            font-size: 20px;
            font-weight: bold;
            color: #007acc;
            margin-bottom: 5px;
        }

        .employee-position {
            font-size: 15px;
            color: #444;
            margin-bottom: 8px;
        }

        .employee-email {
            font-size: 14px;
            color: #555;
        }

        @media (max-width: 768px) {
            .employee-entry {
                width: 100%;
                flex-direction: column;
                align-items: flex-start;
            }

            .employee-photo,
            .no-photo {
                margin-right: 0;
                margin-bottom: 15px;
            }

            .employee-info {
                align-items: center;
            }
        }

        a.employee-entry {
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>

<body>

    <?php include 'header.html'; ?>

    <h1>Зв’яжіться з нами!</h1>
    <div class="description">
        Нижче ви знайдете контактну інформацію наших ключових спеціалістів, які завжди готові відповісти на ваші запитання, допомогти з розміщенням реклами або надати професійну консультацію.
    </div>

    <div class="controls">
        <form method="get">
            <input class="search-input" type="text" name="search" placeholder="Пошук за іменем або прізвищем" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <button class="search-btn" type="submit">Пошук</button>
        </form>
        <?php if ($role !== 'guest'): ?>
            <a href="add_employee.php" class="add-btn">Додати</a>
        <?php endif; ?>
    </div>

    <div class="employees-container">
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($emp = mysqli_fetch_assoc($result)) {
                $wrapperStart = ($role === 'registered') ? '<a href="edit_employee.php?id=' . $emp['employee_id'] . '" class="employee-entry clickable">' : '<div class="employee-entry">';
                $wrapperEnd = ($role === 'registered') ? '</a>' : '</div>';

                echo $wrapperStart;
                if ($emp['photo']) {
                    echo '<img class="employee-photo" src="' . htmlspecialchars($emp['photo']) . '" alt="' . htmlspecialchars($emp['first_name']) . '">';
                } else {
                    echo '<div class="no-photo">Фото<br>відсутнє</div>';
                }
                echo '<div class="employee-info">';
                echo '<div class="employee-name">' . htmlspecialchars($emp['first_name']) . ' ' . htmlspecialchars($emp['last_name']) . '</div>';
                echo '<div class="employee-position">' . htmlspecialchars($emp['position']) . '</div>';
                echo '<div class="employee-email">Email: ' . htmlspecialchars($emp['employee_email']) . '</div>';
                echo '</div>';
                echo $wrapperEnd;
            }
        } else {
            echo '<p style="text-align:center; color:#666;">Записів не знайдено.</p>';
        }
        mysqli_close($connection);
        ?>
    </div>

    <?php include 'footer.html'; ?>

</body>

</html>