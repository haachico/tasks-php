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

if ($jwt) {
    try {
        $decoded = JWT::decode($jwt, new Key($secret_key, 'HS256'));    
        $user_id = $decoded->data->id;
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(["message" => "Invalid token"]);
        exit;
    }
} else {
    http_response_code(401);
    echo json_encode(["message" => "Authorization header not found"]);
    exit;
} 


// print_r($data); // Debugging line to check input data

try {

    $db = new Database();
    $conn = $db->getConnection();

    $data = json_decode(file_get_contents("php://input"), true);

    if(empty($data['id'])) {
        http_response_code(400);
        echo json_encode(["message" => "Task ID is required."]);
        exit();
    }

    $intval = intval($data['id']);

    $query = "DELETE FROM tasks WHERE id = :id AND user_id = :user_id";
    $stmt = $conn->prepare($query);

    if($stmt->execute([':id' => $intval, ':user_id' => $user_id])) {
        if($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(["message" => "Task deleted successfully."]);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Task not found."]);
        }
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Failed to delete task."]);
    }

}
catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "An error occurred: " . $e->getMessage()]);
    exit();
}
