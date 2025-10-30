<?php
require_once 'includes/db.php';

// Het inladen van de gegevens en het uitvoeren er van 
$stmt = $pdo->prepare("SELECT * FROM autos ORDER BY auto_id DESC");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$newsSql = "SELECT nieuws_id, title, content, poster_news, posted_at
              FROM nieuws
              ORDER BY posted_at DESC";
$newsStmt = $pdo->prepare($newsSql);
$newsStmt->execute();
$news = $newsStmt->fetchAll(PDO::FETCH_ASSOC);

// filters gedeelte in laden merk distinct vooegt dubbele damen <> niet gelijk aan 
$merkStmt = $pdo->query("SELECT DISTINCT merk FROM autos WHERE merk <> '' ORDER BY merk ASC");
$merken = $merkStmt->fetchAll(PDO::FETCH_COLUMN);

// filters gedeelte in laden merk distinct vooegt dubbele damen <> niet gelijk aan 
$modelStmt = $pdo->query("SELECT DISTINCT model FROM autos WHERE model <> '' ORDER BY model ASC");
$modelen = $modelStmt->fetchAll(PDO::FETCH_COLUMN);

// filters gedeelte in laden merk distinct vooegt dubbele damen <> niet gelijk aan 
$carrosserieStmt = $pdo->query("SELECT DISTINCT carrosserie FROM autos WHERE carrosserie <> '' ORDER BY carrosserie ASC");
$carrosseries = $carrosserieStmt->fetchAll(PDO::FETCH_COLUMN);

// filters gedeelte in laden merk distinct vooegt dubbele damen <> niet gelijk aan 
$brandstofStmt = $pdo->query("SELECT DISTINCT brandstof FROM autos WHERE brandstof <> '' ORDER BY brandstof ASC");
$brandstofen = $brandstofStmt->fetchAll(PDO::FETCH_COLUMN);

// filters gedeelte in laden merk distinct vooegt dubbele damen <> niet gelijk aan 
$bouwjaarStmt = $pdo->query("SELECT DISTINCT bouwjaar FROM autos WHERE bouwjaar <> '' ORDER BY bouwjaar ASC");
$bouwjaren = $bouwjaarStmt->fetchAll(PDO::FETCH_COLUMN);

// filters gedeelte in laden merk distinct vooegt dubbele damen <> niet gelijk aan 
$prijsStmt = $pdo->query("SELECT DISTINCT prijs FROM autos WHERE prijs <> '' ORDER BY prijs ASC");
$prijzen = $prijsStmt->fetchAll(PDO::FETCH_COLUMN);

// Contact form verwerken
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {

  $naam = $_POST['naam'] ?? '';
  $email = $_POST['email'] ?? '';
  $telefoon = $_POST['telefoon'] ?? '';

  $stmt = $pdo->prepare("
    INSERT INTO contact (naam, email, telefoon)
    VALUES (:naam, :email, :telefoon)
  ");
  
  $stmt->execute([
    ':naam' => $naam,
    ':email' => $email,
    ':telefoon' => $telefoon
  ]);
}

?>


<!DOCTYPE html>
<html lang="nl">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Van der Ven Autos</title>
  <link rel="stylesheet" href="css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&display=swap" rel="stylesheet">
  <script src="js/filter.js"></script>
  <script src="js/hamburger.js" defer></script>

</head>

<body>
  <!-- Header -->
  <header>
    <nav>
      <img src="img/logo.png" alt="ven_logo">

      <button class="hamburger" id="navToggle" aria-controls="mainNav" aria-expanded="false">
        <span class="hamburger-box"><span class="hamburger-inner"></span></span>
      </button>

      <ul>
        <li><a href="#">Home</a></li>
        <li><a href="#carNewsContainer">Actueel auto nieuws</a></li>
        <li><a href="#interesse">Contact</a></li>
        <li><a href="cms/index.php">CMS</a></li>
      </ul>
    </nav>
    <div class="nav-overlay" id="navOverlay" hidden></div>
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
          <h1>WELKOM BIJ</h1>
          <h1>VENAUTO.NL</h1>
          <p>3885 klanten beoordeelden Van der Ven</p>
          <p>Auto’s met gemiddeld een 9,3</p>
          <div class="heroButtons">
            <button type="button" onclick="location.href='#interesse'">
              Contact
            </button>
          </div>
        </div>

        <!-- Filter -->
        <section class="filterContainer">
          <h2>Ons aanbod filter</h2>
          <div class="filterCategorie">
            <label for="brand">Merk:</label>
            <select id="brand" name="Merk">
              <option value="">Alle merken</option>
              <?php foreach ($merken as $merk): ?>
                <option value="<?= htmlspecialchars($merk) ?>">
                  <?= htmlspecialchars($merk) ?>
                </option>
              <?php endforeach; ?>
            </select>

            <label for="model">Model:</label>
            <select id="model" name="Model">
              <option value="">Model:</option>
              <?php foreach ($modelen as $model): ?>
                <option value="<?= htmlspecialchars($model) ?>">
                  <?= htmlspecialchars($model) ?>
                </option>
              <?php endforeach; ?>
            </select>

            <label for="carrosserie">Carrosserie:</label>
            <select id="carrosserie" name="Carrosserie">
              <option value="">Carrosserie:</option>
              <?php foreach ($carrosseries as $carrosserie): ?>
                <option value="<?= htmlspecialchars(strtolower($carrosserie)) ?>">
                  <?= htmlspecialchars($carrosserie) ?>
                </option>
              <?php endforeach; ?>
            </select>

            <label for="brandstof">Brandstof:</label>
            <select id="brandstof" name="Brandstof">
              <option value="">Brandstofen:</option>
              <?php foreach ($brandstofen as $brandstof): ?>
                <option value="<?= htmlspecialchars(strtolower($brandstof)) ?>">
                  <?= htmlspecialchars($brandstof) ?>
                </option>
              <?php endforeach; ?>
            </select>

            <label for="bouwjaar">Bouwjaar:</label>
            <select id="bouwjaar" name="Bouwjaar">
              <option value="">Bouwjaren:</option>
              <?php foreach ($bouwjaren as $bouwjaar): ?>
                <option value="<?= (int) $bouwjaar ?>">
                  <?= (int) $bouwjaar ?>
                </option>
              <?php endforeach; ?>
            </select>

            <label for="prijs">Prijs:</label>
            <select id="prijs" name="Prijs">
              <option value="">Prijzen:</option>
              <?php foreach ($prijzen as $prijs): ?>
                <option value="<?= (int) $prijs ?>">
                  <?= 'Tot €' . number_format((int) $prijs, 0, ',', '.') ?>
                </option>
              <?php endforeach; ?>
            </select>

            <button href="#carCard" type="button" class="filterBtn">Vinden (1000)</button>
          </div>
        </section>
      </div>
    </section>

    <!-- Nieuws -->
    <section id="carNewsContainer">
      <div class="newsGrid">
        <?php if (!empty($news)): ?>
          <?php foreach ($news as $n): ?>
            <?php
            // zorgt er voor dat je niet per se in de db img/ moet neerzetten 
            $img = trim((string) $n['poster_news']);
            ?>
            <article class="newsCard">
              <h3><?php echo htmlspecialchars($n['title']); ?></h3>
              <p><?php echo htmlspecialchars($n['content']); ?></p>
              <?php if ($img !== ''): ?>
                <img src="<?php echo htmlspecialchars($img); ?>" alt="">
              <?php endif; ?>
              <!-- zorgt voor de juist format ipv amerikaanse datum -->
              <small><?php echo date('d-m-Y H:i', strtotime($n['posted_at'])); ?></small>
            </article>
          <?php endforeach; ?>
        <?php else: ?>
          <p>Geen nieuwsartikelen gevonden.</p>
        <?php endif; ?>
      </div>
    </section>

    <!-- auto producten -->
    <section id="carProducts">
      <!-- Het checken of de gegevens er zijn en anders niks weergeven-->
      <?php if (!empty($rows)): ?>
        <?php foreach ($rows as $row): ?>
          <?php
          // Zorg dat het pad klopt (DB kan 'img/...' of alleen 'bestandsnaam.png' bevatten)
          $img = trim($row['poster_auto']);
          ?>
          <!-- geeft structeur is een html5 ingeboude artiekel html 5 -->
          <article class="carCard" data-merk="<?= htmlspecialchars(strtolower($row['merk'])) ?>"
            data-model="<?= htmlspecialchars(strtolower($row['model'])) ?>"
            data-carrosserie="<?= htmlspecialchars(strtolower($row['carrosserie'])) ?>"
            data-brandstof="<?= htmlspecialchars(strtolower($row['brandstof'])) ?>"
            data-bouwjaar="<?= (int) $row['bouwjaar'] ?>" data-prijs="<?= (int) $row['prijs'] ?>">
            <div class="media">
              <img src="<?php echo htmlspecialchars($img ?: 'img/placeholder.png'); ?>"
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
  <section id="interesse" class="interest">
    <h3>Interesse?</h3>
    <p>Laat je gegevens achter, we nemen contact met je op.</p>
    <form class="interest-form" method="post">
      <label>Naam
        <input type="text" name="naam" placeholder="Voor- en achternaam">
      </label>

      <label>E-mail
        <input type="email" name="email" placeholder="naam@voorbeeld.nl">
      </label>

      <label>Telefoon
        <input type="tel" name="telefoon" placeholder="06 12345678">
      </label>

      <button type="submit" name="contact_submit">Verstuur</button>
    </form>
  </section>
</body>

</html>