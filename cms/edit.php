<?php
include '../includes/db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE productID = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $imagePath = $product['image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        if ($imagePath && file_exists('../' . ltrim($imagePath, './'))) {
            unlink('../' . ltrim($imagePath, './'));
        }
        $uploadDir = '../img/products/';
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $newName = uniqid('img_') . '.' . $ext;
        $imagePath = $uploadDir . $newName;
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
        $imagePath = str_replace('../', './', $imagePath);
    }

    $stmt = $pdo->prepare("UPDATE products SET 
        product_name = ?, 
        product_price = ?, 
        product_description = ?, 
        product_categorie = ?, 
        product_console = ?, 
        product_review = ?, 
        product_quantity = ?, 
        product_sale = ?, 
        image = ?
        WHERE productID = ?");

    $stmt->execute([
        $_POST['product_name'],
        $_POST['product_price'],
        $_POST['product_description'],
        $_POST['product_categorie'],
        $_POST['product_console'],
        $_POST['product_review'],
        $_POST['product_quantity'],
        isset($_POST['product_sale']) ? 1 : 0,
        $imagePath,
        $id
    ]);

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Product bewerken</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./css/style.css">
</head>
<body>
  <h1>Product bewerken</h1>
  <form method="POST" enctype="multipart/form-data">
    <label for="product_name">Naam</label>
    <input name="product_name" value="<?= htmlspecialchars($product['product_name']) ?>">

    <label for="product_price">Prijs</label>
    <input name="product_price" type="number" step="0.01" value="<?= $product['product_price'] ?>">

    <label for="product_description">Beschrijving</label>
    <textarea name="product_description" rows="4"><?= htmlspecialchars($product['product_description']) ?></textarea>

    <label for="product_categorie">Categorie</label>
    <input name="product_categorie" value="<?= htmlspecialchars($product['product_categorie']) ?>">

    <label for="product_console">Console</label>
    <input name="product_console" value="<?= htmlspecialchars($product['product_console']) ?>">

    <label for="product_review">Reviewscore (1-5)</label>
    <input name="product_review" type="number" step="0.1" min="0" max="5" value="<?= $product['product_review'] ?>">

    <label for="product_quantity">Voorraad</label>
    <input name="product_quantity" type="number" value="<?= $product['product_quantity'] ?>">

    <label>
      <input type="checkbox" name="product_sale" <?= $product['product_sale'] ? 'checked' : '' ?>> In de aanbieding
    </label>

    <label>Huidige afbeelding</label>
    <?php if ($product['image']): ?>
      <div class="image-preview">
        <img src="<?= str_replace('./', '/rodygamestore/', $product['image']) ?>" alt="productafbeelding">
      </div>
    <?php endif; ?>

    <label for="image">Nieuwe afbeelding</label>
    <input type="file" name="image" accept="image/*">

    <button type="submit">Bijwerken</button>
  </form>

  <a href="index.php" class="back-link">← Terug naar productoverzicht</a>
</body>
</html>
