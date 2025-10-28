<?php
require_once '../includes/db.php';

        // Variable die de gegevens inlaad 
$stmt = $pdo->prepare("SELECT * FROM autos ORDER BY auto_id DESC");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$newsSql = "SELECT nieuws_id, title, content, poster_news, posted_at
            FROM nieuws
            ORDER BY posted_at DESC";
$newsStmt = $pdo->prepare($newsSql);
$newsStmt->execute();
$news = $newsStmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // neemt het email uit de form zonder validatie 
    $email = $_POST['email'] ?? '';

    // sla op in de database 
    $newsbrief = $pdo->prepare("INSERT INTO nieuwsbrief (email) VALUES (:email)");
    $newsbrief->execute([':email' => $email]);
}

?>


<!DOCTYPE html>
<html lang="nl">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Van der Ven Autos</title>
  <link rel="stylesheet" href="../css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&display=swap" rel="stylesheet">
</head>

<body>
  <!-- Header -->
  <header>
    <nav>
      <img src="../img/logo.png" alt="ven_logo">
      <ul>
        <li><a href="../index.php">Home</a></li>
        <li><a href="../index.php#carNewsContainer">Actueel auto nieuws</a></li>
        <li><a href="../contactPagina/index.php">Contact</a></li>
        <li><a href="../cms/index.php">CMS</a></li>
        <li><a href="#">Auto zoeken</a></li>
      </ul>
    </nav>
  </header>

  <!-- Main -->
  <main>
    <!-- Hero sectie -->
    <section class="hero">
      <div class="heroVideoWrapper">
        <iframe id="heroVideo"
          src="https://www.youtube.com/embed/DXH2cTHNreI?autoplay=1&mute=1&start=1&loop=1&playlist=DXH2cTHNreI"
          title="Hero video" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen>
        </iframe>
        <div class="heroOverlay"></div>
      </div>

      <div class="heroInner">
        <!-- Welkom tekst -->
        <div class="heroContent">
          <h1>ONS AANBOD !</h1>
          <h1>VENAUTO.NL</h1>
          <p>3885 klanten beoordeelden Van der Ven</p>
          <p>Auto’s met gemiddeld een 9,3</p>
        </div>

        <!-- Filter -->
        <section class="filterContainer">
          <h2>Ons aanbod filter</h2>
          <div class="filterCategorie">
            <label for="brand">Merk:</label>
            <select id="brand" name="Merk">
              <option value="tesla">Tesla</option>
              <option value="volkswagen">Volkswagen</option>
              <option value="audi">Audi</option>
            </select>

            <label for="model">Model:</label>
            <select id="model" name="Model">
              <option value="model3">Model 3</option>
              <option value="golf">Golf</option>
              <option value="a3">A3</option>
            </select>

            <label for="carrosserie">Carrosserie:</label>
            <select id="carrosserie" name="Carrosserie">
              <option value="sedan">Sedan</option>
              <option value="suv">SUV</option>
              <option value="hatchback">Hatchback</option>
            </select>

            <label for="brandstof">Brandstof:</label>
            <select id="brandstof" name="Brandstof">
              <option value="elektrisch">Elektrisch</option>
              <option value="benzine">Benzine</option>
              <option value="diesel">Diesel</option>
            </select>

            <label for="bouwjaar">Bouwjaar:</label>
            <select id="bouwjaar" name="Bouwjaar">
              <option value="2024">2024</option>
              <option value="2023">2023</option>
              <option value="2022">2022</option>
            </select>

            <label for="prijs">Prijs:</label>
            <select id="prijs" name="Prijs">
              <option value="10000">Tot €10.000</option>
              <option value="20000">Tot €20.000</option>
              <option value="30000">Tot €30.000</option>
            </select>

            <button type="button" class="filterBtn">Vinden (1000)</button>
          </div>
        </section>
      </div>
    </section>
    <!-- auto producten -->
    <section id="carProducts">
      <?php if (!empty($rows)): ?>
        <?php foreach ($rows as $row): ?>
          <?php
          // Zorg dat het pad klopt (DB kan 'img/...' of alleen 'bestandsnaam.png' bevatten)
          $img = trim($row['poster_auto']);
          if ($img !== '' && strpos($img, 'img/') !== 0) {
            $img = 'img/' . $img;
          }
          ?>
          <!-- geeft structeur is een html5 artiekel kaart -->
          <article class="carCard">
            <div class="media">
              <img src="../<?php echo htmlspecialchars($img ?: 'img/placeholder.png'); ?>"
                alt="<?php echo htmlspecialchars($row['merk'] . ' ' . $row['model']); ?>">
            </div>

            <div class="content">
              <h3><?php echo htmlspecialchars($row['merk'] . ' ' . $row['model']); ?></h3>

              <div class="meta">
                <?php echo htmlspecialchars($row['bouwjaar']); ?> |
                <?php echo number_format((int) $row['km_stand'], 0, ',', '.'); ?> km |
                <?php echo htmlspecialchars($row['brandstof']); ?> |
                <?php echo htmlspecialchars($row['versnellingsbak']); ?>
              </div>

              <div class="priceRow">
                <span class="buy">
                  €<?php echo number_format((int) $row['prijs'], 0, ',', '.'); ?>
                </span>
                <span class="per">
                  Financieren<br>
                  €<?php echo number_format((int) $row['prijs_financieren'], 0, ',', '.'); ?> p.m.
                </span>
              </div>
            </div>

            <div class="actions">
              <a class="btn" href="detailPagina/index.php?id=<?= (int) $row['auto_id'] ?>">
                Bekijken
              </a>
            </div>

          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <p>Geen auto's gevonden.</p>
      <?php endif; ?>
    </section>
  </main>

  <!-- Footer -->
  <footer>
  <form method="post" action="">
    <label for="email">Je e-mail:</label>
    <input type="email" id="email" name="email" placeholder="info@voorbeeld.nl">
    <button type="submit">Subscribe</button>
  </form>
  </footer>
</body>

</html>