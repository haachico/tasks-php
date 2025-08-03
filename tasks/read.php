<?php
header("Content-Type: application/json");
include_once "../database.php";


require_once "../vendor/autoload.php";


use Firebase\JWT\JWT;
use Firebase\JWT\Key;


$secret_key = "thisishacchicosecretkey_1995"; 


try {

    $db = new Database();
    $conn = $db->getConnection();

    //i will have to get the user ID from the JWT token

  $headers = getallheaders();
  $jwt = str_replace('Bearer ', '', $headers['Authorization'] ?? '');
    


//   print_r($jwt);
//     exit;
    if ($jwt) {
        try {
            $decoded = JWT::decode($jwt, new Key($secret_key, 'HS256'));
            $user_id = $decoded->data->id;
        } catch (Exception $e) {
            echo json_encode(["message" => "Invalid token"]);
            exit;
        }
    } else {
        echo json_encode(["message" => "Authorization header not found"]);
        exit;
    }

    // print_r($user_id);
    // exit;

    $query = "SELECT * FROM tasks WHERE user_id = :user_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    $tasks = [];

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $tasks[] = [
            "id" => $row['id'],
            "title" => $row['title'],
            "description" => $row['description'],
            "completed" => (bool)$row['completed']
        ];
    }   

    if(!empty($tasks)) {
        echo json_encode($tasks);
    } else {
        echo json_encode(["message" => "No tasks found"]);
    }



} catch (Exception $e) {
    echo json_encode(["message" => "Error: " . $e->getMessage()]);
    exit;
}