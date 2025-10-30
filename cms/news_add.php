<?php
require_once '../includes/db.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/style.css?v=<?= filemtime('css/style.css') ?>">

</head>

<body>
    <form action="actions.php" method="post" enctype="multipart/form-data" class="autoForm">
        <h2>Nieuws toevoegen</h2>

        <!-- Actie + id -->
        <input type="hidden" name="type" value="news">
        <input type="hidden" name="action" value="create">
        <input type="hidden" name="id" value="<?= (int) $nieuws['nieuws_id'] ?>">

        <label>Titel
            <input type="text" name="title" required>
        </label>

        <label>Inhoud
            <input type="text" name="content" required>
        </label>

        <label>Afbeelding uploaden (jpg/png/webp)
            <input type="file" name="image" accept="image/*" id="uploadImg">
        </label>

        <div class="imgPreview" style="align-self:end;">
            <img id="preview" src=""
                style="display:'none';max-width:200px;border-radius:12px;border:1px solid #ddd;">
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