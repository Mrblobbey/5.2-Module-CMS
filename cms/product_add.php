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
        <h2>Auto toevoegen</h2>

        <!-- Actie + id -->
        <input type="hidden" name="type" value="car">
        <input type="hidden" name="action" value="create">

        <div class="grid2">
            <label>Merk
                <input type="text" name="merk" required>
            </label>
            <label>Model
                <input type="text" name="model" required>
            </label>
        </div>

        <div class="grid3">
            <label>Carrosserie
                <select name="carrosserie" required>
                    <?php
                    $cval = $auto['carrosserie'] ?? '';
                    foreach (['Sedan', 'Hatchback', 'SUV', 'Coupé', 'Station', 'Cabrio', 'Overig'] as $opt) {
                        echo "<option value=\"$opt\">$opt</option>";
                    }
                    ?>
                </select>
            </label>

            <label>Bouwjaar
                <input type="number" name="bouwjaar" min="1950" max="2100" required>
            </label>

            <label>KM-stand
                <input type="number" name="km_stand" min="0" required>
            </label>
            <label>Vermogen (pk)
                <input type="number" name="vermogen" min="0" step="1">
            </label>

            <label>Kenteken
                <input type="text" name="kenteken" maxlength="20" placeholder="XX-999-X">
            </label>

            <label>Fabriekskleur
                <input type="text" name="fabrieks_kleur" maxlength="50" placeholder="Midnight Silver">
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
                        echo "<option value=\"$opt\">$opt</option>";
                    }
                    ?>
                </select>
            </label>

            <label>Prijs (koop)
                <input type="number" name="prijs" min="0" required>
            </label>
        </div>

        <label>Prijs (financieren / maand)
            <input type="number" name="prijs_financieren" min="0" required>
        </label>

        <div class="grid2">
            <label>Afbeelding uploaden (jpg/png/webp)
                <input type="file" name="image" accept="image/*" id="uploadImg">
            </label>

            <div class="imgPreview" style="align-self:end;">
                <img id="preview" src=""
                    style="display: 'none';max-width:200px;border-radius:12px;border:1px solid #ddd;">
            </div>
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