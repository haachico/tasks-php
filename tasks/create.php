<?php
header("Content-Type: application/json");
include_once "../database.php";

require_once "../vendor/autoload.php";


use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secret_key = "thisishacchicosecretkey_1995"; 

$data = json_decode(file_get_contents("php://input"), true);



  $headers = getallheaders();
  $jwt = str_replace('Bearer ', '', $headers['Authorization'] ?? '');


//   print_r($jwt);
//   exit;

    if ($jwt) {
        try {
            $decoded = JWT::decode($jwt, new Key($secret_key, 'HS256'));
            $user_id = $decoded->data->id;
        } catch (Exception $e) {
            echo json_encode(["message" => "Invalid token"]);
            exit;
        }
    }
    else {
        echo json_encode(["message" => "Authorization header not found"]);
        exit;
    }

$title = $data['title'] ?? '';
$description = $data['description'] ?? '';


if (!empty($title)) {
    $db = new Database();
    $conn = $db->getConnection();
    $query = "INSERT INTO tasks (title, description, completed, user_id) VALUES (?, ?, 0, ?)";
    $stmt = $conn->prepare($query);
    $success = $stmt->execute([$title, $description, $user_id]);

    if($success){
        echo json_encode(["message" => "Task added successfully"]);
    } else {
        echo json_encode(["message" => "Failed to add task"]);
    }
}
