<?php

$host = "localhost";

$username = "root";

$password = "";

$database = "ewu_innovation_hub";



// Create connection

$conn = new mysqli(
    $host,
    $username,
    $password,
    $database
);  



// Check connection

if($conn->connect_error){

    die("Database Connection Failed: " . $conn->connect_error);

}

//echo "Database Connected";

?>