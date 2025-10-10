<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $imagePath = '';

    // ✅ Afbeelding uploaden als er een bestand is gekozen
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadDir = __DIR__ . '/../img/blogposts/';
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $newName = uniqid('blog_') . '.' . $ext;
        $fullPath = $uploadDir . $newName;

        // Verplaats bestand
        if (move_uploaded_file($_FILES['image']['tmp_name'], $fullPath)) {
            $imagePath = 'img/blogposts/' . $newName; // relatief pad voor in database
        } else {
            die("❌ Upload mislukt: controleer of de map $uploadDir bestaat en schrijfbaar is.");
        }
    }

    // Voeg blog toe aan database
    $stmt = $pdo->prepare("INSERT INTO blogposts (title, image, content) VALUES (?, ?, ?)");
    $stmt->execute([$title, $imagePath, $content]);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <title>Nieuwe blog toevoegen</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <h1>Nieuwe blog toevoegen</h1>

  <form method="post" enctype="multipart/form-data">
    <label>Titel</label>
    <input type="text" name="title" required>

    <label>Afbeelding (optioneel)</label>
    <input type="file" name="image" accept="image/*">

    <label>Inhoud</label>
    <textarea name="content" rows="6" required></textarea>

    <button type="submit" class="btn black">Toevoegen</button>
    <a href="index.php" class="back-link">Annuleren</a>
  </form>
</body>
</html>
