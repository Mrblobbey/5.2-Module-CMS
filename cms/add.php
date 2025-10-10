<?php
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $imagePath = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadDir = '../img/products/';
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $newName = uniqid('img_') . '.' . $ext;
        $imagePath = $uploadDir . $newName;
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
        $imagePath = str_replace('../', './', $imagePath);
    }

    $stmt = $pdo->prepare("INSERT INTO products 
        (productID, product_name, product_price, product_description, product_categorie, product_console, product_review, product_quantity, product_sale, image)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $_POST['productID'],
        $_POST['product_name'],
        $_POST['product_price'],
        $_POST['product_description'],
        $_POST['product_categorie'],
        $_POST['product_console'],
        $_POST['product_review'],
        $_POST['product_quantity'],
        isset($_POST['product_sale']) ? 1 : 0,
        $imagePath
    ]);

    header("Location: index.php");
    exit;
}
?>

<h1>Nieuw product</h1>
<form method="POST" enctype="multipart/form-data">
    ID: <input name="productID" required><br>
    Naam: <input name="product_name" required><br>
    Prijs: <input name="product_price" type="number" step="0.01" required><br>
    Beschrijving:<br><textarea name="product_description" rows="4" cols="40"></textarea><br>
    Categorie: <input name="product_categorie"><br>
    Console: <input name="product_console"><br>
    Review (1-5): <input name="product_review" type="number" step="0.1" min="0" max="5"><br>
    Aantal: <input name="product_quantity" type="number"><br>
    In de aanbieding? <input type="checkbox" name="product_sale"><br>
    Afbeelding: <input type="file" name="image" accept="image/*"><br>
    <button type="submit">Opslaan</button>
</form>
<a href="index.php">⬅️ Terug</a>
