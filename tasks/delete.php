<?php
header("Content-Type: application/json");
include_once "../database.php";

$data = json_decode(file_get_contents("php://input"), true);

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

    $query = "DELETE FROM tasks WHERE id = :id";
    $stmt = $conn->prepare($query);

    if($stmt->execute([':id' => $intval])) {
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
