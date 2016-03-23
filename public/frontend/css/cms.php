<?php

header("Content-type: text/css; charset: UTF-8");

$servername = "192.168.48.200";
//$servername = "192.168.48.43";
$username = "root";
$password = "xv242a";
$dbname = "ocw";

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

<!--body {background-color: #<?php // echo $color ?> !important;}

* {font-family: 'monospace', sans-serif !important;}-->
