<?php
require_once __DIR__ . '/config.php';

// Admin-only CRUD for product variants
// DEV MODE: Skip auth check for testing
// if(empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin'){
//     http_response_code(401); jsonResponse(['ok'=>false,'error'=>'Unauthorized']);
// }

$method = $_SERVER['REQUEST_METHOD'];

// GET - list variants for a product
if($method === 'GET'){
    $productId = isset($_GET['product_id']) ? floatval($_GET['product_id']) : 0;
    if($productId){
        $stmt = $pdo->prepare("SELECT v.variant_id, v.product_id, v.sku, v.color, v.size_eu, v.is_active,
                               COALESCE(i.stock_qty, 0) AS stock_qty
                               FROM product_variants v
                               LEFT JOIN inventory i ON v.variant_id = i.variant_id
                               WHERE v.product_id = :pid ORDER BY v.variant_id");
        $stmt->execute([':pid' => $productId]);
        $rows = $stmt->fetchAll();
        jsonResponse(['ok'=>true,'data'=>$rows]);
    } else {
        // all variants
        $stmt = $pdo->query("SELECT v.variant_id, v.product_id, v.sku, v.color, v.size_eu, v.is_active,
                             COALESCE(i.stock_qty, 0) AS stock_qty
                             FROM product_variants v
                             LEFT JOIN inventory i ON v.variant_id = i.variant_id
                             ORDER BY v.product_id, v.variant_id LIMIT 1000");
        $rows = $stmt->fetchAll();
        jsonResponse(['ok'=>true,'data'=>$rows]);
    }
}

// POST - create new variant
if($method === 'POST'){
    $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $productId = isset($data['product_id']) ? floatval($data['product_id']) : 0;
    $sku = trim($data['sku'] ?? '');
    $color = trim($data['color'] ?? '');
    $sizeEu = trim($data['size_eu'] ?? '');
    $stockQty = isset($data['stock_qty']) ? intval($data['stock_qty']) : 0;
    $isActive = isset($data['is_active']) ? intval($data['is_active']) : 1;

    if(!$productId || !$sku || !$color || !$sizeEu){
        http_response_code(422);
        jsonResponse(['ok'=>false,'error'=>'product_id, sku, color, size_eu sú povinné']);
    }

    // generate new variant_id as max+1
    $row = $pdo->query('SELECT COALESCE(MAX(variant_id), 0) AS maxid FROM product_variants')->fetch();
    $newId = floatval($row['maxid']) + 1.00;

    try{
        $pdo->beginTransaction();

        // insert variant
        $stmt = $pdo->prepare('INSERT INTO product_variants (variant_id, product_id, sku, color, size_eu, is_active)
                               VALUES (:vid, :pid, :sku, :color, :size, :active)');
        $stmt->execute([
            ':vid' => $newId,
            ':pid' => $productId,
            ':sku' => $sku,
            ':color' => $color,
            ':size' => $sizeEu,
            ':active' => $isActive
        ]);

        // insert inventory
        $stmt2 = $pdo->prepare('INSERT INTO inventory (variant_id, stock_qty) VALUES (:vid, :qty)');
        $stmt2->execute([':vid' => $newId, ':qty' => $stockQty]);

        $pdo->commit();
        jsonResponse(['ok'=>true,'variant_id'=>$newId]);
    }catch(Exception $e){
        $pdo->rollBack();
        http_response_code(500);
        jsonResponse(['ok'=>false,'error'=>'Insert failed','detail'=>$e->getMessage()]);
    }
}

// PUT - update variant
if($method === 'PUT'){
    parse_str(file_get_contents('php://input'), $put);
    $variantId = isset($put['variant_id']) ? floatval($put['variant_id']) : 0;
    if(!$variantId){
        http_response_code(400);
        jsonResponse(['ok'=>false,'error'=>'Missing variant_id']);
    }

    try{
        $pdo->beginTransaction();

        // update variant fields
        $fields = [];
        $params = [':vid' => $variantId];
        if(isset($put['sku'])){ $fields[] = 'sku = :sku'; $params[':sku'] = $put['sku']; }
        if(isset($put['color'])){ $fields[] = 'color = :color'; $params[':color'] = $put['color']; }
        if(isset($put['size_eu'])){ $fields[] = 'size_eu = :size'; $params[':size'] = $put['size_eu']; }
        if(isset($put['is_active'])){ $fields[] = 'is_active = :active'; $params[':active'] = intval($put['is_active']); }

        if(!empty($fields)){
            $sql = 'UPDATE product_variants SET ' . implode(', ', $fields) . ' WHERE variant_id = :vid';
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
        }

        // update stock if provided
        if(isset($put['stock_qty'])){
            $stockQty = intval($put['stock_qty']);
            // upsert inventory
            $stmt = $pdo->prepare('INSERT INTO inventory (variant_id, stock_qty) VALUES (:vid, :qty)
                                   ON DUPLICATE KEY UPDATE stock_qty = :qty2');
            $stmt->execute([':vid' => $variantId, ':qty' => $stockQty, ':qty2' => $stockQty]);
        }

        $pdo->commit();
        jsonResponse(['ok'=>true]);
    }catch(Exception $e){
        $pdo->rollBack();
        http_response_code(500);
        jsonResponse(['ok'=>false,'error'=>'Update failed','detail'=>$e->getMessage()]);
    }
}

// DELETE - remove variant
if($method === 'DELETE'){
    parse_str(file_get_contents('php://input'), $del);
    $variantId = isset($del['variant_id']) ? floatval($del['variant_id']) : 0;
    if(!$variantId){
        http_response_code(400);
        jsonResponse(['ok'=>false,'error'=>'Missing variant_id']);
    }

    try{
        $pdo->beginTransaction();

        // delete inventory first (FK)
        $stmt = $pdo->prepare('DELETE FROM inventory WHERE variant_id = :vid');
        $stmt->execute([':vid' => $variantId]);

        // delete variant
        $stmt = $pdo->prepare('DELETE FROM product_variants WHERE variant_id = :vid');
        $stmt->execute([':vid' => $variantId]);

        $pdo->commit();
        jsonResponse(['ok'=>true]);
    }catch(Exception $e){
        $pdo->rollBack();
        http_response_code(500);
        jsonResponse(['ok'=>false,'error'=>'Delete failed','detail'=>$e->getMessage()]);
    }
}

http_response_code(405);
jsonResponse(['ok'=>false,'error'=>'Method not allowed']);
?>

