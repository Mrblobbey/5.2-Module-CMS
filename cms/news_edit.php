<?php
require_once '../includes/db.php';
// Als je bewerkt, zet dan vóór dit formulier $auto vanuit de DB:
$stmt = $pdo->prepare("SELECT * FROM nieuws WHERE nieuws_id = :id");
$stmt->execute([':id' => $_GET['id'] ?? 0]);

if ($stmt->rowCount() == 0) {
    exit();
}

$nieuws = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css?v=<?= filemtime('css/style.css') ?>">

    <title>Document</title>
</head>

<body>
    <form action="actions.php" method="post" enctype="multipart/form-data" class="autoForm">
        <h2>Nieuws bewerken</h2>

        <!-- Actie + id -->
        <input type="hidden" name="type" value="news">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" value="<?= (int) $nieuws['nieuws_id'] ?>">

        <label>Titel
            <input type="text" name="title" value="<?= htmlspecialchars($nieuws['title'] ?? '') ?>" required>
        </label>

        <label>Inhoud
            <input type="text" name="content" value="<?= htmlspecialchars($nieuws['content'] ?? '') ?>" required>
        </label>

        <label>Afbeelding uploaden (jpg/png/webp)
            <input type="file" name="image" accept="image/*" id="uploadImg">
        </label>

        <div class="imgPreview" style="align-self:end;">
            <?php
            $prev = '';
            if (!empty($nieuws['poster_news'])) {
                $prev = htmlspecialchars($nieuws['poster_news']);
            }
            ?>
            <img id="preview" src="<?= $prev ? '../' . $prev : '' ?>"
                style="display:<?= $prev ? 'block' : 'none' ?>;max-width:200px;border-radius:12px;border:1px solid #ddd;">
        </div>

        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <button type="submit" class="btnPrimary">Opslaan</button>
        </div>
    </form>
    <script>
        // Live preview
        document.getElementById('uploadImg')?.addEventListener('change', e => {
            const file = e.target.files?.[0];
            if (!file) return;
            const img = document.getElementById('preview');
            img.src = URL.createObjectURL(file);
            img.style.display = 'block';
        });
    </script>
</body>

</html>