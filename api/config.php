<?php
// Basic DB config and helper functions
header('Content-Type: application/json; charset=utf-8');

$DB_HOST = getenv('DB_HOST') ?: 'db';
$DB_NAME = getenv('DB_NAME') ?: 'eshop';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: 'example';

try{
    $pdo = new PDO("mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}catch(PDOException $e){
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>'DB connection failed','detail'=>$e->getMessage()]);
    exit;
}

function jsonResponse($data){ echo json_encode($data); exit; }

function validateInt($v){ return is_numeric($v) ? intval($v) : null; }

session_start();
?>
