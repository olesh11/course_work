<?php
require_once 'connection.php';
session_start();
$role = $_SESSION['user_role'] ?? 'guest';
$connection = getConnection($role);

$searchTerm = trim($_GET['search'] ?? '');
$sql = "SELECT Campaigns.*, Clients.client_name 
        FROM Campaigns 
        JOIN Clients ON Campaigns.client_id = Clients.client_id";

if (!empty($searchTerm)) {
    $searchTermEscaped = "%" . $connection->real_escape_string($searchTerm) . "%";
    $sql .= " WHERE Campaigns.campaign_name LIKE '$searchTermEscaped'";
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
    <title>Рекламні кампанії</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        h1 {
            text-align: center;
            color: #007acc;
            margin-bottom: 15px;
        }

        .intro-text {
            max-width: 700px;
            margin: 0 auto 40px auto;
            font-size: 16px;
            line-height: 1.6;
            text-align: center;
            color: #444;
        }

        .controls {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .controls form,
        .controls a {
            display: inline-block;
        }

        .campaigns-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .campaign-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            padding: 20px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: transform 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        .campaign-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .campaign-photo {
            width: 120px;
            height: 120px;
            border-radius: 12px;
            object-fit: cover;
            margin-bottom: 15px;
            border: 2px solid #0095e9;
        }

        .no-photo {
            width: 120px;
            height: 120px;
            border-radius: 12px;
            background-color: #ddd;
            color: #777;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            margin-bottom: 15px;
            user-select: none;
        }

        .campaign-name {
            font-weight: bold;
            font-size: 18px;
            color: #007acc;
            margin-bottom: 8px;
            text-align: center;
        }

        .campaign-client {
            font-size: 14px;
            color: #555;
            margin-bottom: 6px;
            font-style: italic;
            text-align: center;
        }

        .campaign-description {
            font-size: 14px;
            color: #333;
            text-align: center;
            margin-bottom: 10px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line: 4;
            -webkit-box-orient: vertical;
            min-height: 80px;
        }

        .search-input {
            padding: 5px;
            font-size: 14px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .search-btn,
        .add-btn {
            padding: 5px 12px;
            font-size: 14px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            background-color: #007acc;
            color: white;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .search-btn:hover,
        .add-btn:hover {
            background-color: #005f99;
        }
    </style>
</head>

<body>

    <?php include 'header.html'; ?>

    <h1>Рекламні кампанії</h1>

    <div class="intro-text">
        Ознайомтесь із поточними рекламними кампаніями наших клієнтів.
    </div>

    <div class="controls">
        <form method="get">
            <input class="search-input" type="text" name="search" placeholder="Пошук за назвою кампанії" value="<?= htmlspecialchars($searchTerm) ?>">
            <button class="search-btn" type="submit">Пошук</button>
        </form>
        <?php if ($role !== 'guest'): ?>
            <a href="add_campaign.php" class="add-btn">Додати кампанію</a>
        <?php endif; ?>
    </div>

    <div class="campaigns-container">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($campaign = mysqli_fetch_assoc($result)): ?>
                <?php
                $tag = ($role === 'registered') ? 'a href="edit_campaign.php?id=' . $campaign['campaign_id'] . '"' : 'div';
                ?>
                <<?= $tag ?> class="campaign-card">
                    <?php if (!empty($campaign['photo'])): ?>
                        <img class="campaign-photo" src="<?= htmlspecialchars($campaign['photo']) ?>" alt="<?= htmlspecialchars($campaign['campaign_name']) ?>">
                    <?php else: ?>
                        <div class="no-photo">Фото відсутнє</div>
                    <?php endif; ?>
                    <div class="campaign-name"><?= htmlspecialchars($campaign['campaign_name']) ?></div>
                    <div class="campaign-client">Клієнт: <?= htmlspecialchars($campaign['client_name']) ?></div>
                    <div class="campaign-description"><?= nl2br(htmlspecialchars($campaign['campaign_description'])) ?></div>
                </<?= explode(' ', $tag)[0] ?>>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center; color:#666;">Кампанії не знайдені.</p>
        <?php endif; ?>
    </div>

    <?php include 'footer.html'; ?>

</body>

</html>