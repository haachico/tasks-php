<?php
header("Content-Type: application/json");
include_once "../database.php";


$data = json_decode(file_get_contents("php://input"), true);

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

    $query = "UPDATE tasks SET title = :title, description = :description WHERE id = :id";
    $stmt = $conn->prepare($query);

    $params = [
        ':id' => intval($data['id']),
        ':title' => $data['title'] ?? null,
        ':description' => $data['description'] ?? null
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