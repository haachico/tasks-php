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
    


try {
 
    $db = new Database();
    $conn = $db->getConnection();

    if(empty($data['id'])) {
        http_response_code(400);
        echo json_encode(["message" => "Task ID is required."]);
        exit();
    }

    if (empty($data['title']) && empty($data['description'])) {
        http_response_code(400);
        echo json_encode(["message" => "At least one field (title or description) must be provided."]);
        exit();
    }


    $query = "UPDATE tasks SET title = :title, description = :description WHERE id = :id AND user_id = :user_id";
    $stmt = $conn->prepare($query);

    $params = [
        ':id' => intval($data['id']),
        ':title' => $data['title'] ?? null,
        ':description' => $data['description'] ?? null,
        ':user_id' => $user_id
    ];

    if($stmt->execute($params)) {
        if($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(["message" => "Task updated successfully."]);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Task not found or no changes made."]);
        }
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Failed to update task."]);
    }

    

}
catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "An error occurred: " . $e->getMessage()]);
    exit();
}