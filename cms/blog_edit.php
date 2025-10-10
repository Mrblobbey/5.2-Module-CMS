<?php
include '../includes/db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM blogposts WHERE id = ?");
$stmt->execute([$id]);
$blog = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $imagePath = $blog['image']; // standaard: bestaande afbeelding behouden

    // ✅ Als er een nieuw bestand is geüpload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadDir = '../img/blog/';
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $newName = uniqid('blog_') . '.' . $ext;
        $fullPath = $uploadDir . $newName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $fullPath)) {
            $imagePath = 'img/blog/' . $newName; // pad relatief t.o.v. de site root
        }
    }

    $stmt = $pdo->prepare("UPDATE blogposts SET title = ?, image = ?, content = ? WHERE id = ?");
    $stmt->execute([$title, $imagePath, $content, $id]);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <title>Blog bewerken</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <h1>Blog bewerken</h1>
  <form method="post" enctype="multipart/form-data">
    <label>Titel</label>
    <input type="text" name="title" value="<?= htmlspecialchars($blog['title']) ?>" required>

    <label>Huidige afbeelding</label><br>
    <img src="/<?= htmlspecialchars($blog['image']) ?>" alt="Afbeelding" style="max-width: 200px;"><br><br>

    <label>Nieuwe afbeelding (optioneel)</label>
    <input type="file" name="image" accept="image/*">

    <label>Inhoud</label>
    <textarea name="content" rows="6" required><?= htmlspecialchars($blog['content']) ?></textarea>

    <button type="submit" class="btn black">Opslaan</button>
    <a href="index.php" class="back-link">Annuleren</a>
  </form>
</body>
</html>
