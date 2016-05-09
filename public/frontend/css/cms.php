<?php

header("Content-type: text/css; charset: UTF-8");

$servername = "localhost";
$username = "admin";
$password = "admin123";
$dbname = "nameDB";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT color_hex FROM cms_colors where color_name = 'body_background'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {

    $color = $result->fetch_assoc()["color_hex"];

} else {
    echo "0 results";
}
$conn->close();
        
?>
