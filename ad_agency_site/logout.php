<?php
session_start();

// Очистити всі дані сесії
$_SESSION = [];

// Знищити сесію
session_destroy();

// Перенаправлення на сторінку входу
header('Location: index.php');
exit();
?>
