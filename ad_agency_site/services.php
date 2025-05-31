<?php
require_once 'connection.php';
session_start();
$role = $_SESSION['user_role'] ?? 'guest';
$connection = getConnection($role);

$searchTerm = $_GET['search'] ?? '';
$sql = "SELECT * FROM Adspace";
if (!empty($searchTerm)) {
    $searchTerm = "%" . $connection->real_escape_string($searchTerm) . "%";
    $sql .= " WHERE ad_type LIKE '$searchTerm' OR ad_description LIKE '$searchTerm'";
}
$sortOrder = $_GET['sort'] ?? '';
if ($sortOrder === 'price_asc') {
    $sql .= " ORDER BY ad_price ASC";
} elseif ($sortOrder === 'price_desc') {
    $sql .= " ORDER BY ad_price DESC";
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
    <title>Послуги</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f2f2f2;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #007acc;
        }

        .controls {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .controls-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }

        .controls form,
        .controls a {
            display: inline-block;
        }

        .search-input {
            padding: 5px;
            font-size: 14px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 250px;
        }

        .search-btn,
        .add-btn {
            padding: 6px 14px;
            font-size: 14px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            background-color: #007acc;
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .adspace-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .adspace-card {
            background-color: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
            cursor: pointer;
            transition: transform 0.3s ease;
            position: relative;
            color: inherit;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            height: 420px;
            /* трохи більше висоти для фото + тексту */
        }

        .adspace-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .adspace-photo {
            width: 100%;
            height: 260px;
            /* більше висоти для фото */
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
            flex-shrink: 0;
        }

        .adspace-type {
            font-weight: bold;
            color: #007acc;
            margin-bottom: 8px;
            flex-shrink: 0;
        }

        .adspace-description {
            font-size: 14px;
            color: #555;
            margin-bottom: 8px;
            flex-grow: 1;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .adspace-price {
            font-weight: bold;
            color: #222;
            flex-shrink: 0;
        }
    </style>
</head>

<body>

    <?php include 'header.html'; ?>

    <h1>Наші рекламні послуги</h1>

    <div class="controls">
        <!-- Перший рядок: пошук -->
        <div class="controls-row">
            <form method="get" action="" style="display: flex; gap: 10px;">
                <input class="search-input" type="text" name="search" placeholder="Пошук за типом або описом"
                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                <button class="search-btn" type="submit">Пошук</button>
            </form>
        </div>

        <!-- Другий рядок: сортування + додавання -->
        <div class="controls-row" style="margin-top: 10px; display: flex; gap: 10px; align-items: center;">
            <form method="get" action="" style="display: flex;">
                <?php if (!empty($_GET['search'])): ?>
                    <input type="hidden" name="search" value="<?= htmlspecialchars($_GET['search']) ?>">
                <?php endif; ?>
                <select name="sort" class="search-input" onchange="this.form.submit()">
                    <option value="">Без сортування</option>
                    <option value="price_asc" <?= ($_GET['sort'] ?? '') === 'price_asc' ? 'selected' : '' ?>>Ціна ↑</option>
                    <option value="price_desc" <?= ($_GET['sort'] ?? '') === 'price_desc' ? 'selected' : '' ?>>Ціна ↓</option>
                </select>
            </form>

            <?php if ($role === 'registered'): ?>
                <a href="add_adspace.php" class="add-btn">Додати послугу</a>
            <?php endif; ?>
        </div>
    </div>


    <div class="adspace-container">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($ad = mysqli_fetch_assoc($result)):
                // Якщо користувач зареєстрований, робимо картку посиланням на редагування
                $cardTag = ($role === 'registered') ? 'a href="edit_adspace.php?id=' . $ad['adspace_id'] . '"' : 'div';
            ?>
                <<?= $cardTag ?> class="adspace-card">
                    <?php if ($ad['photo']): ?>
                        <img class="adspace-photo" src="<?= htmlspecialchars($ad['photo']) ?>" alt="Фото реклами">
                    <?php else: ?>
                        <div class="adspace-photo"
                            style="background-color: #ccc; display: flex; align-items: center; justify-content: center; color: #666;">
                            Фото відсутнє
                        </div>
                    <?php endif; ?>
                    <div class="adspace-type"><?= htmlspecialchars($ad['ad_type']) ?></div>
                    <div class="adspace-description"><?= htmlspecialchars($ad['ad_description']) ?></div>
                    <div class="adspace-price">Ціна: <?= htmlspecialchars($ad['ad_price']) ?> грн/міс</div>
                </<?= explode(' ', $cardTag)[0] ?>>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center; color:#666;">Послуг не знайдено.</p>
        <?php endif; ?>
    </div>

    <?php include 'footer.html'; ?>

</body>

</html>