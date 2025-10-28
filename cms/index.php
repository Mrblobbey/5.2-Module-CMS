<?php
require_once '../includes/db.php';
require_once '../loginPagina/auth.php';


//  verplicht ingelogd
requireLogin('../loginPagina/index.php'); 

$user = $_SESSION['user']; // als je de naam/rol wilt tonen

// Variable merk en model samen om te zorgen dat je de hele title ziet 
$titel = trim(($auto['merk'] ?? '') . ' ' . ($auto['model'] ?? ''));

?>

<!DOCTYPE html>
<html lang="nl">

<head>
  <meta charset="UTF-8">
  <title>CMS Dashboard Autos</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>

  <!--  PRODUCTBEHEER -->
  <header class="cms-header">
    <h1>Productbeheer</h1>
    <a href="add.php" class="btn primary">+ Nieuw product</a>
  </header>

  <main class="product-table-wrapper">
    <table class="product-table">
      <thead>
        <tr>
          <th>Afbeelding</th>
          <th>Merk</th>
          <th>Model</th>
          <th>Carrosserie</th>
          <th>Bouwjaar</th>
          <th>Km stand</th>
          <th>Brandstof</th>
          <th>Versnellingsbak</th>
          <th>Vermogen</th>
          <th>Kenteken</th>
          <th>Fabrieks_kleur</th>
          <th>Prijs</th>
          <th>Prijs P/M</th>
          <th>Acties</th>

        </tr>
      </thead>
      <tbody>
        <?php
        // Variable voor het inladen van de gegevens 
        $stmt = $pdo->query("SELECT * FROM autos ORDER BY auto_id ASC");
        $stmt->execute();
        while ($product = $stmt->fetch()): ?>
          <tr>
            <td>
              <?php if ($product['poster_auto']): ?>
                <img src="../<?= $product['poster_auto'] ?>" class="thumb">
              <?php else: ?>

                <span class="thumb placeholder">Geen</span>
              <?php endif; ?>
            </td>
            <!-- € number_format($product['model'], 2) -->
            <td><?= htmlspecialchars($product['merk']) ?></td>
            <td><?= htmlspecialchars($product['model']) ?></td>
            <td><?= htmlspecialchars($product['carrosserie']) ?></td>
            <!-- zorgt voor alleen cijfers ipv , de int  -->
            <td><?= (int)($product['bouwjaar']) ?></td>
            <td><?= (int)($product['km_stand']) ?></td>
            <td><?= htmlspecialchars($product['brandstof']) ?></td>
            <td><?= htmlspecialchars($product['versnellingsbak']) ?></td>
            <td><?= htmlspecialchars($product['vermogen']) ?></td>
            <td><?= htmlspecialchars($product['kenteken']) ?></td>
            <td><?= htmlspecialchars($product['fabrieks_kleur']) ?></td>
            <td>€<?=number_format($product['prijs'], 2) ?></td>
            <td>€<?=number_format($product['prijs_financieren'], 2) ?></td>

            <td>
              <a href="edit.php?id=<?= $product['productID'] ?>" class="btn small">Bewerken</a>
              <a href="delete.php?id=<?= $product['productID'] ?>" class="btn small red"
                onclick="return confirm('Weet je zeker dat je dit product wilt verwijderen?')">Verwijderen</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </main>

  <!-- nieuws toevoegen -->
  <header class="cms-header" style="margin-top: 60px;">
    <h1>Nieuws Beheer</h1>
    <a href="news_add.php" class="btn primary">+ Nieuws subject</a>
  </header>

  <main class="product-table-wrapper">
    <table class="product-table-news">
      <thead>
        <tr>
          <th>Afbeelding</th>
          <th>Titel</th>
          <th>Inhoud</th>
          <th>Acties</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Variable voor het inladen van de gegevens 
        $stmt_news = $pdo->query("SELECT * FROM nieuws ORDER BY nieuws_id DESC");
        $stmt_news ->execute();
        while ($news = $stmt_news->fetch()): ?>
          <tr>
            <td>
              <?php if (!empty($news['poster_news'])): ?>
                <img src="../img/<?= $news['poster_news'] ?>" class="thumb">
              <?php else: ?>
                <span class="thumb placeholder">Geen Afbeelding</span>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($news['title']) ?></td>
            <td><?= htmlspecialchars(mb_strimwidth($news['content'], 0, 100, '...')) ?></td>
            <td>
              <a href="news_edit.php?id=<?= $news['id'] ?>" class="btn small">Bewerken</a>
              <a href="news_delete.php?id=<?= $news['id'] ?>" class="btn small red"
                onclick="return confirm('Weet je zeker dat je deze news wilt verwijderen?')">Verwijderen</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </main>