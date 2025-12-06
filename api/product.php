<?php
require_once __DIR__ . '/config.php';
// GET product by id
if($_SERVER['REQUEST_METHOD'] === 'GET'){
    $id = isset($_GET['id']) ? floatval($_GET['id']) : 0;
    if(!$id){ http_response_code(400); jsonResponse(['ok'=>false,'error'=>'Missing id']); }
    try{
        // detect image_url column
        $hasImageCol = false;
        try{ $cstmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='products' AND COLUMN_NAME='image_url'"); $cstmt->execute(); $cres = $cstmt->fetch(); $hasImageCol = intval($cres['cnt'])>0; }catch(Exception $e){ $hasImageCol = false; }

        $cols = 'product_id, sku_model, name, brand, gender, base_price, description, is_active';
        if($hasImageCol) $cols .= ', image_url';
        $stmt = $pdo->prepare("SELECT $cols FROM products WHERE product_id = :id LIMIT 1");
        $stmt->execute([':id'=>$id]);
        $p = $stmt->fetch();
        if(!$p){ http_response_code(404); jsonResponse(['ok'=>false,'error'=>'Not found']); }
        // variants
        $vstmt = $pdo->prepare('SELECT variant_id, sku, color, size_eu, is_active FROM product_variants WHERE product_id = :id');
        $vstmt->execute([':id'=>$id]);
        $variants = $vstmt->fetchAll();
        // inventory for variants
        $variantIds = array_map(function($v){ return $v['variant_id']; }, $variants);
        $inventory = [];
        if(count($variantIds)>0){
            $in = implode(',', array_fill(0, count($variantIds), '?'));
            $istmt = $pdo->prepare("SELECT variant_id, stock_qty FROM inventory WHERE variant_id IN ($in)");
            $istmt->execute($variantIds);
            $invRows = $istmt->fetchAll();
            foreach($invRows as $ir){ $inventory[$ir['variant_id']] = intval($ir['stock_qty']); }
        }
        foreach($variants as &$vv){ $vid = $vv['variant_id']; $vv['stock_qty'] = isset($inventory[$vid]) ? $inventory[$vid] : 0; }
        unset($vv);
        // images placeholder or stored
        $images = [];
        if($hasImageCol && !empty($p['image_url'])){ $images[] = $p['image_url']; }
        $images[] = "https://picsum.photos/seed/p{$p['product_id']}/800/600";
        $p['base_price'] = floatval($p['base_price']);
        jsonResponse(['ok'=>true,'data'=>['product'=>$p,'variants'=>$variants,'images'=>$images]]);
    }catch(Exception $e){ http_response_code(500); jsonResponse(['ok'=>false,'error'=>'Query failed','detail'=>$e->getMessage()]); }
}
http_response_code(405); jsonResponse(['ok'=>false,'error'=>'Method not allowed']);
?>
