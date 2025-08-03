<?php

header("Content-Type: application/json");
include_once "../database.php";


require_once "../vendor/autoload.php";


use Firebase\JWT\JWT;
use Firebase\JWT\Key;


$secret_key = "thisishacchicosecretkey_1995"; 

$data = json_decode(file_get_contents("php://input"), true);


try {

    if(empty($data['id'])) {
        http_response_code(400);
        echo json_encode(["message" => "Task ID is required."]);
        exit();
    }

    $db = new Database();
    $conn = $db->getConnection();

    $query = "UPDATE tasks SET completed = NOT completed WHERE id = :id";
    $stmt = $conn->prepare($query);
    
    $invalidId = intval($data['id']);
    $params = [':id' => $invalidId];

    if($stmt->execute($params)) {
        if($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(["message" => "Task completion status toggled successfully."]);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Task not found."]);
        }
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Failed to toggle task completion status."]);
    }

}
catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "An error occurred: " . $e->getMessage()]);
    exit();
}