<?php
require_once __DIR__ . '/config.php';
// Admin-only CRUD for products (basic)
if(empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin'){
    http_response_code(401); jsonResponse(['ok'=>false,'error'=>'Unauthorized']);
}

// detect if image_url column exists
$hasImageCol = false;
try{
    $cstmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='products' AND COLUMN_NAME='image_url'");
    $cstmt->execute(); $cres = $cstmt->fetch(); $hasImageCol = intval($cres['cnt'])>0;
}catch(Exception $e){ $hasImageCol = false; }

$method = $_SERVER['REQUEST_METHOD'];
if($method === 'GET'){
    // list all products (map product_id -> id, base_price -> price for compatibility with admin UI)
    $cols = 'product_id AS id, name, sku_model, brand, base_price AS price, is_active';
    if($hasImageCol) $cols .= ', image_url';
    $stmt = $pdo->query("SELECT $cols FROM products ORDER BY product_id DESC LIMIT 1000");
    $rows = $stmt->fetchAll();
    jsonResponse(['ok'=>true,'data'=>$rows]);
}

if($method === 'POST'){
    $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $name = trim($data['name'] ?? '');
    $price = isset($data['price']) ? floatval($data['price']) : null;
    if($name === '' || $price === null){ http_response_code(422); jsonResponse(['ok'=>false,'errors'=>['name'=>'required','price'=>'required']]); }

    // generate new product_id (DECIMAL) as max+1
    $row = $pdo->query('SELECT COALESCE(MAX(product_id), 0) AS maxid FROM products')->fetch();
    $newId = floatval($row['maxid']) + 1.00;

    $stmt = $pdo->prepare('INSERT INTO products (product_id, sku_model, name, brand, gender, base_price, description, is_active' . ($hasImageCol ? ', image_url' : '') . ') VALUES (:pid, :sku, :name, :brand, :gender, :price, :desc, :active' . ($hasImageCol ? ', :img' : '') . ')');
    try{
        $params = [
            ':pid' => $newId,
            ':sku' => $data['sku_model'] ?? null,
            ':name' => $name,
            ':brand' => $data['brand'] ?? null,
            ':gender' => $data['gender'] ?? null,
            ':price' => $price,
            ':desc' => $data['description'] ?? null,
            ':active' => isset($data['is_active']) ? intval($data['is_active']) : 1
        ];
        if($hasImageCol) {
            $params[':img'] = $data['image_url'] ?? null;
        }
        $stmt->execute($params);
        jsonResponse(['ok'=>true,'id'=>$newId]);
    }catch(Exception $e){ http_response_code(500); jsonResponse(['ok'=>false,'error'=>'Insert failed','detail'=>$e->getMessage()]); }
}

if($method === 'PUT'){
    parse_str(file_get_contents('php://input'), $put);
    $id = isset($put['id']) ? floatval($put['id']) : 0;
    if(!$id) { http_response_code(400); jsonResponse(['ok'=>false,'error'=>'Missing id']); }
    $fields = [];
    $params = [':pid'=>$id];
    if(isset($put['name'])){ $fields[] = 'name = :name'; $params[':name'] = $put['name']; }
    if(isset($put['price'])){ $fields[] = 'base_price = :price'; $params[':price'] = floatval($put['price']); }
    if(isset($put['sku_model'])){ $fields[] = 'sku_model = :sku'; $params[':sku'] = $put['sku_model']; }
    if(isset($put['brand'])){ $fields[] = 'brand = :brand'; $params[':brand'] = $put['brand']; }
    if(isset($put['gender'])){ $fields[] = 'gender = :gender'; $params[':gender'] = $put['gender']; }
    if(isset($put['description'])){ $fields[] = 'description = :desc'; $params[':desc'] = $put['description']; }
    if(isset($put['is_active'])){ $fields[] = 'is_active = :active'; $params[':active'] = intval($put['is_active']); }
    if($hasImageCol && isset($put['image_url'])){ $fields[] = 'image_url = :img'; $params[':img'] = $put['image_url']; }
    if(empty($fields)){ http_response_code(400); jsonResponse(['ok'=>false,'error'=>'No fields']); }
    $sql = 'UPDATE products SET ' . implode(', ', $fields) . ' WHERE product_id = :pid';
    $stmt = $pdo->prepare($sql);
    try{ $stmt->execute($params); jsonResponse(['ok'=>true]); }catch(Exception $e){ http_response_code(500); jsonResponse(['ok'=>false,'error'=>'Update failed','detail'=>$e->getMessage()]); }
}

if($method === 'DELETE'){
    parse_str(file_get_contents('php://input'), $del);
    $id = isset($del['id']) ? floatval($del['id']) : 0;
    if(!$id){ http_response_code(400); jsonResponse(['ok'=>false,'error'=>'Missing id']); }
    $stmt = $pdo->prepare('DELETE FROM products WHERE product_id = :pid');
    try{ $stmt->execute([':pid'=>$id]); jsonResponse(['ok'=>true]); }catch(Exception $e){ http_response_code(500); jsonResponse(['ok'=>false,'error'=>'Delete failed','detail'=>$e->getMessage()]); }
}

http_response_code(405); jsonResponse(['ok'=>false,'error'=>'Method not allowed']);
?>
