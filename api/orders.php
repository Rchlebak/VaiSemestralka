<?php
require_once __DIR__ . '/config.php';
// POST create order
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $payload = json_decode(file_get_contents('php://input'), true);
    if(!$payload){ http_response_code(400); jsonResponse(['ok'=>false,'error'=>'Invalid JSON']); }
    // basic validation
    $name = trim($payload['customer_name'] ?? '');
    $email = trim($payload['customer_email'] ?? '');
    $address = trim($payload['customer_address'] ?? '');
    $cart = $payload['cart'] ?? [];
    $errors = [];
    if(strlen($name) < 2) $errors['customer_name'] = 'Meno je povinné';
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['customer_email'] = 'Neplatný email';
    if(strlen($address) < 10) $errors['customer_address'] = 'Adresu zadajte podrobne';
    if(!is_array($cart) || count($cart)===0) $errors['cart'] = 'Košík je prázdny';
    if(!empty($errors)){ http_response_code(422); jsonResponse(['ok'=>false,'errors'=>$errors]); }

    // compute total and insert order
    $total = 0.0;
    foreach($cart as $it){ $total += floatval($it['price']) * intval($it['qty']); }
    $pdo->beginTransaction();
    try{
        $stmt = $pdo->prepare('INSERT INTO orders (order_number, customer_name, customer_email, customer_address, total) VALUES (:order_number,:name,:email,:addr,:total)');
        $orderNumber = 'ORD'.time().rand(100,999);
        $stmt->execute([':order_number'=>$orderNumber, ':name'=>$name, ':email'=>$email, ':addr'=>$address, ':total'=>$total]);
        $orderId = $pdo->lastInsertId();
        $itStmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, variant_id, quantity, unit_price) VALUES (:order_id,:product_id,:variant_id,:quantity,:unit_price)');
        foreach($cart as $it){
            $itStmt->execute([':order_id'=>$orderId, ':product_id'=>$it['productId'], ':variant_id'=> $it['variantId'] ?? null, ':quantity'=>$it['qty'], ':unit_price'=>$it['price']]);
            // decrease stock if variant
            if(!empty($it['variantId'])){
                $u = $pdo->prepare('UPDATE product_variants SET stock = GREATEST(0, stock - :q) WHERE id = :id');
                $u->execute([':q'=>$it['qty'], ':id'=>$it['variantId']]);
            }
        }
        $pdo->commit();
        jsonResponse(['ok'=>true,'order_id'=>$orderId,'order_number'=>$orderNumber]);
    }catch(Exception $e){ $pdo->rollBack(); http_response_code(500); jsonResponse(['ok'=>false,'error'=>'Could not create order']); }
}
http_response_code(405); jsonResponse(['ok'=>false,'error'=>'Method not allowed']);
?>
