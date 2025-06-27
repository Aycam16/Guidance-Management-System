<?php
    $host = "localhost";
    $user = "root";
    $password = "";
    $database = "qcu(1)";
    $port =3306;
    // Create connection
    $conn = mysqli_connect($host, $user, $password, $database, $port);

    // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
?>
