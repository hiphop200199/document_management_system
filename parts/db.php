<?php
$servername = "localhost";
$username = "root";
$password = "";

try {
  $conn = new PDO("mysql:host=$servername;dbname=document_management_system", $username, $password); 
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);//下這道才會用真的prepare statement
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}
?>