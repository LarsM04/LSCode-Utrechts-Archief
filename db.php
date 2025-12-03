<?php
$host = "localhost";
$dbname = "hua_panorama";
$user = "root";      
$pass = "";          

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}
?>

