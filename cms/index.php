<?php
include '../includes/db.php';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <title>CMS Dashboard</title>
  <link rel="stylesheet" href="./css/style.css">
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
          <th>Naam</th>
          <th>Prijs</th>
          <th>Categorie</th>
          <th>Console</th>
          <th>Voorraad</th>
          <th>Acties</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $stmt = $pdo->query("SELECT * FROM products ORDER BY product_name ASC");
        foreach ($stmt as $product): ?>
          <tr>
            <td>
              <?php if ($product['image']): ?>
                <img src="<?= str_replace('./', '/rodygamestore/', $product['image']) ?>" class="thumb">
              <?php else: ?>
                <span class="thumb placeholder">Geen</span>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($product['product_name']) ?></td>
            <td>€<?= number_format($product['product_price'], 2) ?>
              <?php if ($product['product_sale']): ?><span class="badge">Sale</span><?php endif; ?>
            </td>
            <td><?= htmlspecialchars($product['product_categorie']) ?></td>
            <td><?= htmlspecialchars($product['product_console']) ?></td>
            <td><?= $product['product_quantity'] ?></td>
            <td>
              <a href="edit.php?id=<?= $product['productID'] ?>" class="btn small">Bewerken</a>
              <a href="delete.php?id=<?= $product['productID'] ?>" class="btn small red" onclick="return confirm('Weet je zeker dat je dit product wilt verwijderen?')">Verwijderen</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </main>

  <!-- BLOGBEHEER -->
<header class="cms-header" style="margin-top: 60px;">
  <h1>Blogbeheer</h1>
  <a href="blog_add.php" class="btn primary">+ Nieuwe blog</a>
</header>

<main class="product-table-wrapper">
  <table class="product-table">
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
      $stmt = $pdo->query("SELECT * FROM blogposts ORDER BY id DESC");

      foreach ($stmt as $blog): ?>
        <tr>
          <td>
            <?php if (!empty($blog['image'])): ?>
              <img src="<?= str_replace('./', '/rodygamestore/', $blog['image']) ?>" class="thumb">
            <?php else: ?>
              <span class="thumb placeholder">Geen</span>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($blog['title']) ?></td>
          <td><?= htmlspecialchars(mb_strimwidth($blog['content'], 0, 100, '...')) ?></td>
          <td>
            <a href="blog_edit.php?id=<?= $blog['id'] ?>" class="btn small">Bewerken</a>
            <a href="blog_delete.php?id=<?= $blog['id'] ?>" class="btn small red" onclick="return confirm('Weet je zeker dat je deze blog wilt verwijderen?')">Verwijderen</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</main>
