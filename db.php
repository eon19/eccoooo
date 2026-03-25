<?php
$conn = new mysqli("localhost", "root", "", "market");
if($conn->connect_error){
    die("Connection  failed");
}
?>