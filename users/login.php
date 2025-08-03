<?php

header("Content-Type: application/json");
include_once "../database.php";
require_once "../vendor/autoload.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secret_key = "thisishacchicosecretkey_1995"; 


$data = json_decode(file_get_contents("php://input"), true);

$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

if(empty($username) || empty($password)) {
  echo json_encode([
    "status" => "error",
    "message" => "Username and password are required."
  ]);
  exit;
}

$db = new Database();
$connection = $db->getConnection();

$query = "SELECT * FROM users WHERE username = :username";
$stmt = $connection->prepare($query);
$stmt->bindParam(':username', $username);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password'])) {
  echo json_encode([
    "status" => "error",
    "message" => "Invalid username or password."
  ]);
  exit;
}

// JWT token generation
$issuedAt = time();
$expirationTime = $issuedAt + 600; // Token valid for 10 minutes
$payload = [
    "iss" => "http://localhost", // Issuer
    "iat" => $issuedAt,           // Issued at
    "exp" => $expirationTime,     // Expiration
    "data" => [
        "id" => $user['id'],
        "username" => $user['username']
    ]
];

$jwt = JWT::encode($payload, $secret_key, 'HS256');

echo json_encode([
  "status" => "success",
  "message" => "Login successful.",
  "token" => $jwt,
  "user" => [
    "id" => $user['id'],
    "username" => $user['username']
  ]
]);
exit;
?>