<?php
require_once '../includes/db.php';
// checkt of de id geldig is en in de url staat 
if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
  $stmt = $pdo->prepare("SELECT * FROM autos WHERE auto_id = :id");
  $stmt->execute([':id' => $_GET['id']]);
  $auto = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$auto) {
    die("Auto niet gevonden!");
  }
} else {
  die("Ongeldige ID!");
}


function h($v)
{
  return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}

function eur($v)
{
  if ($v === null || $v === '')
    return '';
  // Maak allerlei invoer veilig: “€ 29.000,-”, “29.000”, “29000”, etc.
  $digits = preg_replace('/\D+/', '', (string) $v); // laat alleen cijfers over
  if ($digits === '')
    return '';
  return '€ ' . number_format((int) $digits, 0, ',', '.');
}

// zorgt er voor dat er een punt komt en netjes uitgelijnd wordt 
function km($v)
{
  if ($v === null || $v === '')
    return '';
  // laat alleen cijfers over: "12.000 km" -> "12000"
  $digits = preg_replace('/\D+/', '', (string) $v);
  if ($digits === '')
    return '';
  return number_format((int) $digits, 0, ',', '.') . ' km';
}


$poster = $auto['poster_auto'];
// om de titel goed neet te zetten 
$titel = trim(($auto['merk'] ?? '') . ' ' . ($auto['model'] ?? ''));

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
  <script src="../js/hamburger.js" defer></script>

</head>

<body>
  <!-- Header -->
  <header>
    <nav>
      <img src="../img/logo.png" alt="ven_logo">

      <button class="hamburger" id="navToggle" aria-controls="mainNav" aria-expanded="false">
        <span class="hamburger-box"><span class="hamburger-inner"></span></span>
      </button>

      <ul>
        <li><a href="../index.php">Home</a></li>
        <li><a href="../index.php#carNewsContainer">Actueel auto nieuws</a></li>
        <li><a href="../index.php#interesse">Contact</a></li>
        <li><a href="../cms/index.php">CMS</a></li>
      </ul>
    </nav>

    <div class="nav-overlay" id="navOverlay" hidden></div>


  </header>

  <!-- Main -->
  <main>
    <!-- BOVENSTE BLOK: afbeelding links, verkoop rechts -->
    <section id="main_product" class="detail-grid">
      <!-- Linker kolom: grote poster + thumbnails -->
      <section id="product_poster" class="poster">
        <div class="poster-frame">
          <!-- het in laden van de img en template tekst img -->
          <img src="../<?= h($poster) ?>" alt="<?= h($titel) ?>" class="poster-main">
        </div>
      </section>

      <!-- Rechter kolom: verkoopinformatie -->
      <section id="car_sales_info" class="sales">
        <h1 class="title"><?= h($auto['merk']) ?></h1>
        <p class="subtitle"><?= h($auto['model']) ?></p>
        <p class="meta">
          <?= h($auto['bouwjaar']) ?> |
          <?= isset($auto['km_stand']) ? h(km($auto['km_stand'])) : '—' ?> |
          <?= h($auto['brandstof']) ?>
        </p>
        <div class="price_card">
          <span class="price">
            <!-- twee keuzes mocht er geen info dan prijs op aanvraag? -->
            <?= isset($auto['prijs']) ? h(eur($auto['prijs'])) : 'Prijs op aanvraag' ?>
          </span>
          <span class="finance_label">Financieren</span>
          <span class="finance">€ <?= h($auto['prijs_financieren']) ?>,- p.m.</span>
          <a class="cta" href="#interesse">Ik heb interesse</a>
        </div>
      </section>
    </section>

    <!-- specificaties  -->
    <section id="car_technical" class="specs">
      <h2>Specificaties van deze <strong><?= h($titel) ?></strong></h2>
      <!-- Inhoud: je kunt met CSS de niet-actieve verbergen -->
      <div class="tabpanes">
        <!-- BASIS -->
        <div class="tabpane is-active" id="pane-basis" data-tab="basis">
          <table class="spec-table">
            <tbody>
              <tr>
                <th>Kenteken</th>
                <td><?= h($auto['kenteken']) ?></td>
              </tr>
              <tr>
                <th>Kilometerstand</th>
                <td><?= isset($auto['km_stand']) ? h(km($auto['km_stand'])) : '—' ?></td>
              </tr>
              <tr>
                <th>Bouwjaar</th>
                <td><?= h($auto['bouwjaar']) ?></td>
              </tr>
              <tr>
                <th>Transmissie</th>
                <td><?= h($auto['versnellingsbak']) ?></td>
              </tr>
              <tr>
                <th>Carrosserie</th>
                <td><?= h($auto['carrosserie']) ?></td>
              </tr>
              <tr>
                <th>Vermogen</th>
                <td><?= h($auto['vermogen']) ?></td>
              </tr>
              <th>Brandstof</th>
              <td><?= h($auto['brandstof']) ?></td>
              </tr>
              <tr>
                <th>Fabriekskleur</th>
                <td><?= h($auto['fabrieks_kleur']) ?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <!-- Interesse-anker -->
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
  </main>

</body>

</html>