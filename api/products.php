<?php
require_once __DIR__ . '/config.php';

// GET: list products with simple filters and their variants + inventory
if($_SERVER['REQUEST_METHOD'] === 'GET'){
    $q = isset($_GET['q']) ? trim($_GET['q']) : null;
    $min = isset($_GET['min_price']) ? floatval($_GET['min_price']) : null;
    $max = isset($_GET['max_price']) ? floatval($_GET['max_price']) : null;

    // detect if products table has image_url column (legacy)
    $hasImageCol = false;
    try{
        $cstmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='products' AND COLUMN_NAME='image_url'");
        $cstmt->execute();
        $cres = $cstmt->fetch();
        $hasImageCol = intval($cres['cnt']) > 0;
    }catch(Exception $e){ $hasImageCol = false; }

    // check if product_images table exists
    $hasImagesTable = false;
    try{
        $cstmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='product_images'");
        $cstmt->execute();
        $cres = $cstmt->fetch();
        $hasImagesTable = intval($cres['cnt'])>0;
    }catch(Exception $e){ $hasImagesTable = false; }

    $sql = "SELECT product_id, name, sku_model, brand, base_price, description" . ($hasImageCol?", image_url":"") . " FROM products WHERE is_active = 1";
    $params = [];
    if($q){ $sql .= " AND (name LIKE :q OR description LIKE :q OR sku_model LIKE :q OR brand LIKE :q)"; $params[':q'] = "%$q%"; }
    if($min !== null){ $sql .= " AND base_price >= :min"; $params[':min'] = $min; }
    if($max !== null){ $sql .= " AND base_price <= :max"; $params[':max'] = $max; }
    $sql .= " ORDER BY product_id DESC LIMIT 200";

    try{
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll();

        if(!$products){ jsonResponse(['ok'=>true,'data'=>[]]); }

        // collect product ids
        $pids = array_map(function($r){ return $r['product_id']; }, $products);

        // fetch main images from product_images table
        $imagesByProduct = [];
        if($hasImagesTable && count($pids) > 0){
            $in = implode(',', array_fill(0, count($pids), '?'));
            $imgStmt = $pdo->prepare("SELECT product_id, image_path FROM product_images WHERE product_id IN ($in) AND is_main = 1");
            $imgStmt->execute($pids);
            $imgRows = $imgStmt->fetchAll();
            foreach($imgRows as $ir){
                $imagesByProduct[$ir['product_id']] = $ir['image_path'];
            }
            // Fallback: get first image if no main image set
            $missingPids = array_diff($pids, array_keys($imagesByProduct));
            if(count($missingPids) > 0){
                $in2 = implode(',', array_fill(0, count($missingPids), '?'));
                $imgStmt2 = $pdo->prepare("SELECT product_id, image_path FROM product_images WHERE product_id IN ($in2) ORDER BY sort_order ASC, image_id ASC");
                $imgStmt2->execute(array_values($missingPids));
                $imgRows2 = $imgStmt2->fetchAll();
                foreach($imgRows2 as $ir){
                    if(!isset($imagesByProduct[$ir['product_id']])){
                        $imagesByProduct[$ir['product_id']] = $ir['image_path'];
                    }
                }
            }
        }

        // fetch variants for these products
        $in = implode(',', array_fill(0, count($pids), '?'));
        $vstmt = $pdo->prepare("SELECT variant_id, product_id, sku, color, size_eu, is_active FROM product_variants WHERE product_id IN ($in)");
        $vstmt->execute($pids);
        $variantsAll = $vstmt->fetchAll();

        // fetch inventory for variant ids
        $variantIds = array_map(function($v){ return $v['variant_id']; }, $variantsAll);
        $inventory = [];
        if(count($variantIds) > 0){
            $in2 = implode(',', array_fill(0, count($variantIds), '?'));
            $istmt = $pdo->prepare("SELECT variant_id, stock_qty FROM inventory WHERE variant_id IN ($in2)");
            $istmt->execute($variantIds);
            $invRows = $istmt->fetchAll();
            foreach($invRows as $ir){ $inventory[$ir['variant_id']] = intval($ir['stock_qty']); }
        }

        // organize variants by product_id
        $variantsByProduct = [];
        foreach($variantsAll as $v){
            $vid = $v['variant_id'];
            $v['stock_qty'] = isset($inventory[$vid]) ? $inventory[$vid] : 0;
            $variantsByProduct[$v['product_id']][] = $v;
        }

        // map products to output structure expected by frontend
        $out = [];
        foreach($products as $p){
            $pid = $p['product_id'];
            $vars = isset($variantsByProduct[$pid]) ? $variantsByProduct[$pid] : [];
            // derive sizes and colors
            $sizes = [];
            $colors = [];
            foreach($vars as $vv){ if(!in_array($vv['size_eu'], $sizes)) $sizes[] = $vv['size_eu']; if(!in_array($vv['color'], $colors)) $colors[] = $vv['color']; }

            // Get image: 1. from product_images table, 2. legacy image_url, 3. placeholder
            $imageUrl = null;
            if(isset($imagesByProduct[$pid])){
                $imageUrl = $imagesByProduct[$pid];
            } elseif($hasImageCol && !empty($p['image_url'])){
                $imageUrl = $p['image_url'];
            } else {
                $imageUrl = "https://picsum.photos/seed/p{$pid}/400/300";
            }

            $out[] = [
                'id' => $pid,
                'product_id' => $pid,
                'name' => $p['name'],
                'sku_model' => $p['sku_model'],
                'brand' => $p['brand'],
                'price' => floatval($p['base_price']),
                'old_price' => null,
                'description' => $p['description'],
                'image' => $imageUrl,
                'variants' => $vars,
                'sizes' => $sizes,
                'colors' => $colors
            ];
        }

        jsonResponse(['ok'=>true,'data'=>$out]);
    }catch(Exception $e){ http_response_code(500); jsonResponse(['ok'=>false,'error'=>'Query failed','detail'=>$e->getMessage()]); }
}

http_response_code(405);
jsonResponse(['ok'=>false,'error'=>'Method not allowed']);
?>
