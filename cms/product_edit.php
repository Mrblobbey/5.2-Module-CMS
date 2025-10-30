<?php
require_once '../includes/db.php';
// Als je bewerkt, zet dan vóór dit formulier $auto vanuit de DB:
$stmt = $pdo->prepare("SELECT * FROM autos WHERE auto_id = :id");
$stmt->execute([':id' => $_GET['id'] ?? 0]);

if ($stmt->rowCount() == 0) {
    exit();
}

$auto = $stmt->fetch(PDO::FETCH_ASSOC);
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
        <h2>Auto bewerken</h2>

        <!-- Actie + id -->
        <input type="hidden" name="type" value="car">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" value="<?= (int) $auto['auto_id'] ?>">

        <!-- Als je al een poster hebt: doorgeven zodat we hem kunnen behouden als je niets uploadt -->
        <input type="hidden" name="old_poster"
            value="<?= isset($auto['poster_auto']) ? htmlspecialchars($auto['poster_auto']) : '' ?>">

        <div class="grid2">
            <label>Merk
                <input type="text" name="merk" value="<?= htmlspecialchars($auto['merk'] ?? '') ?>" required>
            </label>
            <label>Model
                <input type="text" name="model" value="<?= htmlspecialchars($auto['model'] ?? '') ?>" required>
            </label>
        </div>

        <div class="grid3">
            <label>Carrosserie
                <select name="carrosserie" required>
                    <?php
                    $cval = $auto['carrosserie'] ?? '';
                    foreach (['Sedan', 'Hatchback', 'SUV', 'Coupé', 'Station', 'Cabrio', 'Overig'] as $opt) {
                        $sel = ($opt === $cval) ? 'selected' : '';
                        echo "<option value=\"$opt\" $sel>$opt</option>";
                    }
                    ?>
                </select>
            </label>

            <label>Bouwjaar
                <input type="number" name="bouwjaar" min="1950" max="2100"
                    value="<?= htmlspecialchars($auto['bouwjaar'] ?? '') ?>" required>
            </label>

            <label>KM-stand
                <input type="number" name="km_stand" min="0" value="<?= htmlspecialchars($auto['km_stand'] ?? '') ?>"
                    required>
            </label>
        </div>

        <div class="grid3">
            <label>Brandstof
                <select name="brandstof" required>
                    <?php
                    $fval = $auto['brandstof'] ?? '';
                    foreach (['Benzine', 'Diesel', 'Elektrisch', 'Hybrid', 'LPG'] as $opt) {
                        $sel = ($opt === $fval) ? 'selected' : '';
                        echo "<option value=\"$opt\" $sel>$opt</option>";
                    }
                    ?>
                </select>
            </label>

            <label>Versnellingsbak
                <select name="versnellingsbak" required>
                    <?php
                    $gval = $auto['versnellingsbak'] ?? '';
                    foreach (['Automaat', 'Handgeschakeld'] as $opt) {
                        $sel = ($opt === $gval) ? 'selected' : '';
                        echo "<option value=\"$opt\" $sel>$opt</option>";
                    }
                    ?>
                </select>
            </label>

            <label>Prijs (koop)
                <input type="number" name="prijs" min="0" value="<?= htmlspecialchars($auto['prijs'] ?? '') ?>"
                    required>
            </label>
        </div>

        <label>Prijs (financieren / maand)
            <input type="number" name="prijs_financieren" min="0"
                value="<?= htmlspecialchars($auto['prijs_financieren'] ?? '') ?>" required>
        </label>

        <div class="grid2">
            <label>Afbeelding uploaden (jpg/png/webp)
                <input type="file" name="image" accept="image/*" id="uploadImg">
            </label>

            <div class="imgPreview" style="align-self:end;">
                <?php
                $prev = '';
                if (!empty($auto['poster_auto'])) {
                    $prev = htmlspecialchars($auto['poster_auto']);
                }
                ?>
                <img id="preview" src="<?= $prev ? '../' . $prev : '' ?>"
                    style="display:<?= $prev ? 'block' : 'none' ?>;max-width:200px;border-radius:12px;border:1px solid #ddd;">
            </div>
        </div>

        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <button type="submit" class="btnPrimary">Opslaan</button>
        </div>
    </form>

    <form action="actions.php" method="post"
        onsubmit="return confirm('Weet je zeker dat je deze auto wilt verwijderen?');" style="display:inline;">
        <input type="hidden" name="type" value="car">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="<?= (int) $auto['auto_id'] ?>">
        <button type="submit" class="btnDanger">Verwijderen</button>
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