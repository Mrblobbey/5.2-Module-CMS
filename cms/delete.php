<?php
include '../includes/db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT image FROM products WHERE productID = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

// Verwijder afbeelding
if ($product && $product['image']) {
    $imgPath = '../' . ltrim($product['image'], './');
    if (file_exists($imgPath)) {
        unlink($imgPath);
    }
}

// Verwijder product
$stmt = $pdo->prepare("DELETE FROM products WHERE productID = ?");
$stmt->execute([$id]);

header("Location: index.php");
exit;
