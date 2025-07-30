<?php

header("Content-Type: application/json");
include_once "../database.php";


$data = json_decode(file_get_contents("php://input"), true);

$username = $data['username'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';


if(empty($username) || empty($email) || empty($password)) {
  echo json_encode([
    "status" => "error",
    "message" => "All fields are required."
  ]);
  exit();
}



$db = new Database();
$connection = $db->getConnection();

$query = "SELECT * FROM users WHERE username = :username OR email = :email" ;
$stmt = $connection->prepare($query);
$stmt->bindParam(':username', $username);
$stmt->bindParam(':email', $email);
$stmt->execute();

if($stmt->rowCount() > 0) {
  echo json_encode([
    "status" => "error",
    "message" => "Username or email already exists."
  ]);
} else {
  $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
  
  $insertQuery = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
  $insertStmt = $connection->prepare($insertQuery);
  $insertStmt->bindParam(':username', $username);
  $insertStmt->bindParam(':email', $email);
  $insertStmt->bindParam(':password', $hashedPassword);
  
  if ($insertStmt->execute()) {
    echo json_encode([
      "status" => "success",
      "message" => "User registered successfully."
    ]);
  } else {
    echo json_encode([
      "status" => "error",
      "message" => "Failed to register user."
    ]);
  }
}