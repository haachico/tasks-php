<?php
require_once "../vendor/autoload.php";
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function getUserIdFromJWT($secret_key) {
    $headers = getallheaders();
    $jwt = str_replace('Bearer ', '', $headers['Authorization'] ?? '');
    if ($jwt) {
        try {
            $decoded = JWT::decode($jwt, new Key($secret_key, 'HS256'));
            return $decoded->data->id;
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
}
