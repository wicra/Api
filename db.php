<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$servername = "127.0.0.1";
$port = "8889"; 
$username = "flutter";
$password = "flutter";
$dbname = "flutter_db";

try {
    $conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Connection failed: " . $e->getMessage()]);
    exit();
}
?>