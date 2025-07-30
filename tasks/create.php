<?php
header("Content-Type: application/json");
include_once "../database.php";

$data = json_decode(file_get_contents("php://input"), true);

$title = $data['title'] ?? '';
$description = $data['description'] ?? '';

if (!empty($title)) {
    $db = new Database();
    $conn = $db->getConnection();
    $query = "INSERT INTO tasks (title, description, completed) VALUES (?, ?, 0)";
    $stmt = $conn->prepare($query);
    $success = $stmt->execute([$title, $description]);

    if($success){
        echo json_encode(["message" => "Task added successfully"]);
    } else {
        echo json_encode(["message" => "Failed to add task"]);
    }
}
