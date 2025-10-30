<?php
// cms/actions.php
declare(strict_types=1);

/*
  een actions.php voor zowel nieuws als auto's.
  - Verwacht POST met velden: type, action, id (bij update/delete)
  - type: 'news' of 'auto' (ook 'car' geaccepteerd)
  - actoin: 'create' | 'update' | 'delete'
  - Voor uploads moet je form enctype="multipart/form-data" hebben
*/

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../loginPagina/auth.php';
requireLogin('../loginPagina/index.php');

/*  helpers  */
function redirect(string $url): void {
  header('Location: ' . $url);
  exit;
}

function sanitizeFileName(string $name): string {
  $name = preg_replace('/[^a-zA-Z0-9._-]/','_', $name);
  return strtolower($name);
}

/**
 * haal geüploade afbeelding binnen en plaats in /uploads (relatief vanaf cms/)
 * Retourneert relatieve padstring zoals 'uploads/img_xxx.webp' of null als geen upload
 */
function handleUpload(string $field, string $subdir='uploads'): ?string {
  if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
    return null;
  }
  $tmp  = $_FILES[$field]['tmp_name'];
  $orig = sanitizeFileName($_FILES[$field]['name']);
  $ext  = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
  if (!in_array($ext, ['jpg','jpeg','png','webp'], true)) {
    return null;
  }

  $base = __DIR__ . "/../{$subdir}";
  if (!is_dir($base)) {
    @mkdir($base, 0775, true);
  }

  $targetRel = $subdir . '/' . (uniqid('img_', true) . '.' . $ext);
  $targetAbs = __DIR__ . '/../' . $targetRel;

  if (move_uploaded_file($tmp, $targetAbs)) {
    return $targetRel;
  }
  return null;
}

/*  only POST  */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  redirect('index.php?flash=Geen+actie+(geen+POST)');
}

/*  input  */
$typeRaw = $_POST['type']   ?? '';
$action  = $_POST['action'] ?? '';
$id      = isset($_POST['id']) ? (int)$_POST['id'] : 0;

/* 'auto' en 'car' behandelen hetzelfde */
$type = $typeRaw === 'car' ? 'auto' : $typeRaw;

/*  afhandeling  */
try {

  /* NIEUWS*/
  if ($type === 'news') {

    if ($action === 'create') {
      $title   = trim($_POST['title']   ?? '');
      $content = trim($_POST['content'] ?? '');
      $img     = handleUpload('image'); 

      $stmt = $pdo->prepare("
        INSERT INTO nieuws (title, content, poster_news, posted_at)
        VALUES (:t, :c, :p, NOW())
      ");
      $stmt->execute([
        ':t' => $title,
        ':c' => $content,
        ':p' => $img ?? ''
      ]);

      redirect('index.php?flash=Nieuws+toegevoegd');
    }

    if ($action === 'update' && $id > 0) {
      $title   = trim($_POST['title']   ?? '');
      $content = trim($_POST['content'] ?? '');
      $img     = handleUpload('image'); // nieuwe upload overschrijft

      if ($img) {
        $stmt = $pdo->prepare("
          UPDATE nieuws
             SET title=:t, content=:c, poster_news=:p
           WHERE nieuws_id=:id
        ");
        $stmt->execute([
          ':t'=>$title, ':c'=>$content, ':p'=>$img, ':id'=>$id
        ]);
      } else {
        $stmt = $pdo->prepare("
          UPDATE nieuws
             SET title=:t, content=:c
           WHERE nieuws_id=:id
        ");
        $stmt->execute([
          ':t'=>$title, ':c'=>$content, ':id'=>$id
        ]);
      }

      redirect('index.php?flash=Nieuws+bijgewerkt');
    }

    if ($action === 'delete' && $id > 0) {
      $stmt = $pdo->prepare("DELETE FROM nieuws WHERE nieuws_id=:id");
      $stmt->execute([':id'=>$id]);
      redirect('index.php?flash=Nieuws+verwijderd');
    }

    redirect('index.php?flash=Geen+geldige+actie+(news)');
  }

  /*  AUTO'S */
  if ($type === 'auto') {

    if ($action === 'create') {
      $data = [
        ':merk'   => trim($_POST['merk'] ?? ''),
        ':model'  => trim($_POST['model'] ?? ''),
        ':bouwjaar' => (int)($_POST['bouwjaar'] ?? 0),
        ':km'     => (int)($_POST['km_stand'] ?? 0),
        ':brandstof' => trim($_POST['brandstof'] ?? ''),
        ':versn'  => trim($_POST['versnellingsbak'] ?? ''),
        ':prijs'  => (int)($_POST['prijs'] ?? 0),
        ':pfin'   => (int)($_POST['prijs_financieren'] ?? 0),

        // extra velden
        ':verm'   => (int)($_POST['vermogen'] ?? 0),
        ':kent'   => strtoupper(trim($_POST['kenteken'] ?? '')),
        ':kleur'  => trim($_POST['fabrieks_kleur'] ?? ''),
        ':carro'  => strtolower(trim($_POST['carrosserie'] ?? '')),
      ];
      $img = handleUpload('image');

      $sql = "
        INSERT INTO autos
          (merk, model, bouwjaar, km_stand, brandstof, versnellingsbak,
           prijs, prijs_financieren, poster_auto,
           vermogen, kenteken, fabrieks_kleur, carrosserie)
        VALUES
          (:merk, :model, :bouwjaar, :km, :brandstof, :versn,
           :prijs, :pfin, :img,
           :verm, :kent, :kleur, :carro)
      ";
      $stmt = $pdo->prepare($sql);
      $stmt->execute($data + [':img' => $img ?? '']);

      redirect('index.php?flash=Auto+toegevoegd');
    }

    if ($action === 'update' && $id > 0) {
      $data = [
        ':merk'   => trim($_POST['merk'] ?? ''),
        ':model'  => trim($_POST['model'] ?? ''),
        ':bouwjaar' => (int)($_POST['bouwjaar'] ?? 0),
        ':km'     => (int)($_POST['km_stand'] ?? 0),
        ':brandstof' => trim($_POST['brandstof'] ?? ''),
        ':versn'  => trim($_POST['versnellingsbak'] ?? ''),
        ':prijs'  => (int)($_POST['prijs'] ?? 0),
        ':pfin'   => (int)($_POST['prijs_financieren'] ?? 0),
        ':verm'   => (int)($_POST['vermogen'] ?? 0),
        ':kent'   => strtoupper(trim($_POST['kenteken'] ?? '')),
        ':kleur'  => trim($_POST['fabrieks_kleur'] ?? ''),
        ':carro'  => strtolower(trim($_POST['carrosserie'] ?? '')),
        ':id'     => $id
      ];
      $img = handleUpload('image');

      if ($img) {
        $sql = "
          UPDATE autos SET
            merk=:merk, model=:model, bouwjaar=:bouwjaar, km_stand=:km,
            brandstof=:brandstof, versnellingsbak=:versn,
            prijs=:prijs, prijs_financieren=:pfin, poster_auto=:img,
            vermogen=:verm, kenteken=:kent, fabrieks_kleur=:kleur, carrosserie=:carro
          WHERE auto_id=:id
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data + [':img'=>$img]);
      } else {
        $sql = "
          UPDATE autos SET
            merk=:merk, model=:model, bouwjaar=:bouwjaar, km_stand=:km,
            brandstof=:brandstof, versnellingsbak=:versn,
            prijs=:prijs, prijs_financieren=:pfin,
            vermogen=:verm, kenteken=:kent, fabrieks_kleur=:kleur, carrosserie=:carro
          WHERE auto_id=:id
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
      }

      redirect('index.php?flash=Auto+bijgewerkt');
    }

    if ($action === 'delete' && $id > 0) {
      // voorbeeld:
      // $pdo->prepare('DELETE FROM fotos WHERE auto_id = :id')->execute([':id'=>$id]);

      // optioneel: huidige poster ophalen en bestand verwijderen na succesvol delete
      $old = $pdo->prepare('SELECT poster_auto FROM autos WHERE auto_id=:id');
      $old->execute([':id'=>$id]);
      $oldRow = $old->fetch(PDO::FETCH_ASSOC);
      $oldFile = $oldRow && !empty($oldRow['poster_auto']) ? (__DIR__ . '/../' . $oldRow['poster_auto']) : null;

      $stmt = $pdo->prepare("DELETE FROM autos WHERE auto_id=:id");
      $ok   = $stmt->execute([':id'=>$id]);

      if ($ok && $oldFile && is_file($oldFile)) {
        @unlink($oldFile); // stil verwijderen, geen fatale error als dit mislukt
      }

      redirect('index.php?flash=Auto+verwijderd');
    }

    redirect('index.php?flash=Geen+geldige+actie+(auto)');
  }

  /* onbekend type */
  redirect('index.php?flash=Onbekend+type');

} catch (Throwable $e) {
  redirect('index.php?flash=' . urlencode('Fout: ' . $e->getMessage()));
}
