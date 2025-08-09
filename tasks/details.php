<?php
header("Content-Type: application/json");
include_once "../database.php";
require_once "../vendor/autoload.php";
require_once "../auth/jwt_helper.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secret_key = "thisishacchicosecretkey_1995";


$headers = getallheaders();
$jwt = str_replace('Bearer ', '', $headers['Authorization'] ?? '');

if ($jwt) {
    try {
        $decoded = JWT::decode($jwt, new Key($secret_key, 'HS256'));
        $user_id = $decoded->data->id;
    }
    catch (Exception $e) {
        http_response_code(401);
        echo json_encode(["message" => "Invalid token"]);
        exit;
    }
} else {
    http_response_code(401);
    echo json_encode(["message" => "Authorization header not found"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$taskId = $data['id'] ?? '';

if (empty($taskId)) {
    http_response_code(400);
    echo json_encode(["message" => "Task ID is required."]);
    exit();
}


try {
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = :id");
    $stmt->bindParam(":id", $taskId);
    $stmt->execute();

    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$task) {
        http_response_code(404);
        echo json_encode(["message" => "Task not found."]);
        exit();
    }

    echo json_encode($task);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Internal Server Error"]);
}