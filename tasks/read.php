<?php
header("Content-Type: application/json");
include_once "../database.php";


try {

    $db = new Database();
    $conn = $db->getConnection();

    $query = "SELECT * FROM tasks";

    $stmt = $conn->prepare($query);
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