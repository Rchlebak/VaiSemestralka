<?php
require_once __DIR__ . '/config.php';

// Admin-only image management
// DEV MODE: Skip auth check for testing
// if(empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin'){
//     http_response_code(401);
//     jsonResponse(['ok'=>false,'error'=>'Unauthorized']);
// }

// Create uploads directory if not exists
$uploadsDir = __DIR__ . '/../uploads';
if(!is_dir($uploadsDir)){
    mkdir($uploadsDir, 0755, true);
}

$method = $_SERVER['REQUEST_METHOD'];

// GET - list images for a product
if($method === 'GET'){
    $productId = isset($_GET['product_id']) ? floatval($_GET['product_id']) : 0;
    if(!$productId){
        http_response_code(400);
        jsonResponse(['ok'=>false,'error'=>'Missing product_id']);
    }

    try {
        $stmt = $pdo->prepare("SELECT image_id, product_id, image_path, sort_order, is_main
                               FROM product_images
                               WHERE product_id = :pid
                               ORDER BY is_main DESC, sort_order ASC, image_id ASC");
        $stmt->execute([':pid' => $productId]);
        $images = $stmt->fetchAll();
        jsonResponse(['ok'=>true, 'data'=>$images]);
    } catch(Exception $e) {
        http_response_code(500);
        jsonResponse(['ok'=>false,'error'=>'Query failed','detail'=>$e->getMessage()]);
    }
}

// POST - upload new image
if($method === 'POST'){
    $productId = isset($_POST['product_id']) ? floatval($_POST['product_id']) : 0;

    if(!$productId){
        http_response_code(400);
        jsonResponse(['ok'=>false,'error'=>'Missing product_id']);
    }

    // Check if it's a file upload or URL
    $imagePath = '';

    if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK){
        // File upload
        $file = $_FILES['image'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if(!in_array($file['type'], $allowedTypes)){
            http_response_code(400);
            jsonResponse(['ok'=>false,'error'=>'Nepovolený typ súboru. Povolené: JPG, PNG, GIF, WEBP']);
        }

        // Max 5MB
        if($file['size'] > 5 * 1024 * 1024){
            http_response_code(400);
            jsonResponse(['ok'=>false,'error'=>'Súbor je príliš veľký (max 5MB)']);
        }

        // Generate unique filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'product_' . intval($productId) . '_' . time() . '_' . uniqid() . '.' . $ext;
        $targetPath = $uploadsDir . '/' . $filename;

        if(!move_uploaded_file($file['tmp_name'], $targetPath)){
            http_response_code(500);
            jsonResponse(['ok'=>false,'error'=>'Nepodarilo sa uložiť súbor']);
        }

        $imagePath = '/uploads/' . $filename;

    } elseif(isset($_POST['image_url']) && !empty($_POST['image_url'])){
        // URL provided
        $imagePath = trim($_POST['image_url']);

        // Basic URL validation
        if(!filter_var($imagePath, FILTER_VALIDATE_URL) && !preg_match('/^\/uploads\//', $imagePath)){
            http_response_code(400);
            jsonResponse(['ok'=>false,'error'=>'Neplatná URL obrázka']);
        }
    } else {
        http_response_code(400);
        jsonResponse(['ok'=>false,'error'=>'Žiadny obrázok nebol nahraný']);
    }

    try {
        // Check if this is the first image (make it main)
        $stmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM product_images WHERE product_id = :pid");
        $stmt->execute([':pid' => $productId]);
        $row = $stmt->fetch();
        $isMain = ($row['cnt'] == 0) ? 1 : 0;

        // Get next sort order
        $stmt = $pdo->prepare("SELECT COALESCE(MAX(sort_order), 0) + 1 AS next_order FROM product_images WHERE product_id = :pid");
        $stmt->execute([':pid' => $productId]);
        $orderRow = $stmt->fetch();
        $sortOrder = intval($orderRow['next_order']);

        // Insert image record
        $stmt = $pdo->prepare("INSERT INTO product_images (product_id, image_path, sort_order, is_main)
                               VALUES (:pid, :path, :order, :main)");
        $stmt->execute([
            ':pid' => $productId,
            ':path' => $imagePath,
            ':order' => $sortOrder,
            ':main' => $isMain
        ]);

        $imageId = $pdo->lastInsertId();

        jsonResponse(['ok'=>true, 'image_id'=>$imageId, 'image_path'=>$imagePath]);

    } catch(Exception $e) {
        http_response_code(500);
        jsonResponse(['ok'=>false,'error'=>'Insert failed','detail'=>$e->getMessage()]);
    }
}

// PUT - update image (set as main, change order)
if($method === 'PUT'){
    parse_str(file_get_contents('php://input'), $put);
    $imageId = isset($put['image_id']) ? intval($put['image_id']) : 0;

    if(!$imageId){
        http_response_code(400);
        jsonResponse(['ok'=>false,'error'=>'Missing image_id']);
    }

    try {
        // Get image info
        $stmt = $pdo->prepare("SELECT product_id FROM product_images WHERE image_id = :iid");
        $stmt->execute([':iid' => $imageId]);
        $img = $stmt->fetch();

        if(!$img){
            http_response_code(404);
            jsonResponse(['ok'=>false,'error'=>'Image not found']);
        }

        // Set as main
        if(isset($put['is_main']) && $put['is_main']){
            // Reset all images for this product
            $stmt = $pdo->prepare("UPDATE product_images SET is_main = 0 WHERE product_id = :pid");
            $stmt->execute([':pid' => $img['product_id']]);

            // Set this one as main
            $stmt = $pdo->prepare("UPDATE product_images SET is_main = 1 WHERE image_id = :iid");
            $stmt->execute([':iid' => $imageId]);
        }

        // Update sort order
        if(isset($put['sort_order'])){
            $stmt = $pdo->prepare("UPDATE product_images SET sort_order = :order WHERE image_id = :iid");
            $stmt->execute([':order' => intval($put['sort_order']), ':iid' => $imageId]);
        }

        jsonResponse(['ok'=>true]);

    } catch(Exception $e) {
        http_response_code(500);
        jsonResponse(['ok'=>false,'error'=>'Update failed','detail'=>$e->getMessage()]);
    }
}

// DELETE - remove image
if($method === 'DELETE'){
    parse_str(file_get_contents('php://input'), $del);
    $imageId = isset($del['image_id']) ? intval($del['image_id']) : 0;

    if(!$imageId){
        http_response_code(400);
        jsonResponse(['ok'=>false,'error'=>'Missing image_id']);
    }

    try {
        // Get image info
        $stmt = $pdo->prepare("SELECT image_path, product_id, is_main FROM product_images WHERE image_id = :iid");
        $stmt->execute([':iid' => $imageId]);
        $img = $stmt->fetch();

        if(!$img){
            http_response_code(404);
            jsonResponse(['ok'=>false,'error'=>'Image not found']);
        }

        // Delete file if it's a local upload
        if(strpos($img['image_path'], '/uploads/') === 0){
            $filePath = __DIR__ . '/..' . $img['image_path'];
            if(file_exists($filePath)){
                unlink($filePath);
            }
        }

        // Delete database record
        $stmt = $pdo->prepare("DELETE FROM product_images WHERE image_id = :iid");
        $stmt->execute([':iid' => $imageId]);

        // If this was main image, set another one as main
        if($img['is_main']){
            $stmt = $pdo->prepare("UPDATE product_images SET is_main = 1
                                   WHERE product_id = :pid
                                   ORDER BY sort_order ASC, image_id ASC
                                   LIMIT 1");
            $stmt->execute([':pid' => $img['product_id']]);
        }

        jsonResponse(['ok'=>true]);

    } catch(Exception $e) {
        http_response_code(500);
        jsonResponse(['ok'=>false,'error'=>'Delete failed','detail'=>$e->getMessage()]);
    }
}

http_response_code(405);
jsonResponse(['ok'=>false,'error'=>'Method not allowed']);
?>

