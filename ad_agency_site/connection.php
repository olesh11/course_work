<?php
function getConnection($userRole = 'guest') {
    $servername = "localhost";

    if ($userRole === 'registered') {
        $username = "registered_user";
        $password = "userpass";
    } else {
        $username = "guest_user";
        $password = "guestpass";
    }

    $dbname = "adagency";
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    return $conn;
}
?>
