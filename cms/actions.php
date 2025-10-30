<?php
require_once __DIR__ . '/../includes/db.php';

function sanitizeFileName($name){
  $name = preg_replace('/[^a-zA-Z0-9._-]/','_', $name);
  return strtolower($name);
}

function handleUpload(string $field, string $subdir='uploads'): ?string {
  if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
    return null;
  }
  $tmp  = $_FILES[$field]['tmp_name'];
  $orig = sanitizeFileName($_FILES[$field]['name']);
  $ext  = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
  if (!in_array($ext, ['jpg','jpeg','png','webp'])) return null;

  if (!is_dir(__DIR__ . "/../$subdir")) {
    @mkdir(__DIR__ . "/../$subdir", 0775, true);
  }
  $targetRel = $subdir . '/' . (uniqid('img_', true) . '.' . $ext);
  $targetAbs = __DIR__ . '/../' . $targetRel;

  if (move_uploaded_file($tmp, $targetAbs)) {
    return $targetRel;   // sla relatieve path op (bijv. 'uploads/img_xyz.webp')
  }
  return null;
}

$type   = $_POST['type']   ?? '';
$action = $_POST['action'] ?? '';
$id     = isset($_POST['id']) ? (int)$_POST['id'] : 0;

try {
  if ($type === 'news') {
    if ($action === 'create') {
      $title = trim($_POST['title'] ?? '');
      $content = trim($_POST['content'] ?? '');
      $img = handleUpload('image'); // optioneel

      $stmt = $pdo->prepare("INSERT INTO nieuws (title, content, poster_news, posted_at)
                             VALUES (:t, :c, :p, NOW())");
      $stmt->execute([':t'=>$title, ':c'=>$content, ':p'=>$img ?? '']);
      header('Location: index.php?flash=Nieuws+toegevoegd'); exit;

    } elseif ($action === 'update' && $id > 0) {
      $title = trim($_POST['title'] ?? '');
      $content = trim($_POST['content'] ?? '');
      $img = handleUpload('image'); // als nieuw geüpload, overschrijven we

      if ($img) {
        $stmt = $pdo->prepare("UPDATE nieuws SET title=:t, content=:c, poster_news=:p WHERE nieuws_id=:id");
        $stmt->execute([':t'=>$title, ':c'=>$content, ':p'=>$img, ':id'=>$id]);
      } else {
        $stmt = $pdo->prepare("UPDATE nieuws SET title=:t, content=:c WHERE nieuws_id=:id");
        $stmt->execute([':t'=>$title, ':c'=>$content, ':id'=>$id]);
      }
      header('Location: index.php?flash=Nieuws+bijgewerkt'); exit;

    } elseif ($action === 'delete' && $id > 0) {
      $stmt = $pdo->prepare("DELETE FROM nieuws WHERE nieuws_id=:id");
      $stmt->execute([':id'=>$id]);
      header('Location: index.php?flash=Nieuws+verwijderd'); exit;
    }
  }

  if ($type === 'car') {
    if ($action === 'create') {
      $data = [
        ':merk' => trim($_POST['merk'] ?? ''),
        ':model' => trim($_POST['model'] ?? ''),
        ':bouwjaar' => (int)($_POST['bouwjaar'] ?? 0),
        ':km' => (int)($_POST['km_stand'] ?? 0),
        ':brandstof' => trim($_POST['brandstof'] ?? ''),
        ':versn' => trim($_POST['versnellingsbak'] ?? ''),
        ':prijs' => (int)($_POST['prijs'] ?? 0),
        ':pfin' => (int)($_POST['prijs_financieren'] ?? 0),
      ];
      $img = handleUpload('image');

      $sql = "INSERT INTO autos
                (merk, model, bouwjaar, km_stand, brandstof, versnellingsbak, prijs, prijs_financieren, poster_auto)
              VALUES
                (:merk, :model, :bouwjaar, :km, :brandstof, :versn, :prijs, :pfin, :img)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute($data + [':img' => $img ?? '']);
      header('Location: index.php?flash=Auto+toegevoegd'); exit;

    } elseif ($action === 'update' && $id > 0) {
      $data = [
        ':merk' => trim($_POST['merk'] ?? ''),
        ':model' => trim($_POST['model'] ?? ''),
        ':bouwjaar' => (int)($_POST['bouwjaar'] ?? 0),
        ':km' => (int)($_POST['km_stand'] ?? 0),
        ':brandstof' => trim($_POST['brandstof'] ?? ''),
        ':versn' => trim($_POST['versnellingsbak'] ?? ''),
        ':prijs' => (int)($_POST['prijs'] ?? 0),
        ':pfin' => (int)($_POST['prijs_financieren'] ?? 0),
        ':id' => $id
      ];
      $img = handleUpload('image');

      if ($img) {
        $sql = "UPDATE autos SET
                  merk=:merk, model=:model, bouwjaar=:bouwjaar, km_stand=:km,
                  brandstof=:brandstof, versnellingsbak=:versn,
                  prijs=:prijs, prijs_financieren=:pfin, poster_auto=:img
                WHERE auto_id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data + [':img'=>$img]);
      } else {
        $sql = "UPDATE autos SET
                  merk=:merk, model=:model, bouwjaar=:bouwjaar, km_stand=:km,
                  brandstof=:brandstof, versnellingsbak=:versn,
                  prijs=:prijs, prijs_financieren=:pfin
                WHERE auto_id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
      }
      header('Location: index.php?flash=Auto+bijgewerkt'); exit;

    } elseif ($action === 'delete' && $id > 0) {
      $stmt = $pdo->prepare("DELETE FROM autos WHERE auto_id=:id");
      $stmt->execute([':id'=>$id]);
      header('Location: index.php?flash=Auto+verwijderd'); exit;
    }
  }

  header('Location: index.php?flash=Geen+actie+uitgevoerd');
} catch (Throwable $e) {
  header('Location: index.php?flash=Fout:+'.urlencode($e->getMessage()));
}
