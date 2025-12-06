<?php
require_once __DIR__ . '/config.php';
// Simple session-based auth for admin using ADMIN_PASS env (development mode)
$action = $_GET['action'] ?? '';
if($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'login'){
    $data = json_decode(file_get_contents('php://input'), true);
    if(!$data){ // try form
        $data = $_POST;
    }
    $username = trim($data['username'] ?? '');
    $password = $data['password'] ?? '';
    if(!$username || !$password){ http_response_code(400); jsonResponse(['ok'=>false,'error'=>'Missing credentials']); }

    $ADMIN_PASS = getenv('ADMIN_PASS') ?: 'admin123';
    if($password !== $ADMIN_PASS){ http_response_code(401); jsonResponse(['ok'=>false,'error'=>'Invalid credentials']); }

    // login as admin
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = $username;
    $_SESSION['role'] = 'admin';
    jsonResponse(['ok'=>true,'user'=>['id'=>1,'username'=>$username,'role'=>'admin']]);
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'logout'){
    session_unset(); session_destroy(); jsonResponse(['ok'=>true]);
}

http_response_code(405); jsonResponse(['ok'=>false,'error'=>'Method not allowed']);
?>
