<?php
require_once '../includes/db.php';
require_once 'auth.php';

$errors = [];
$msg = $_GET['msg'] ?? '';
// hier controlleerd hij of de gegevens die wel echt versuurdt zijn via post kloppen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // checkt of de gegevens in de database bestaan anders geeft hij null mee en dus onjuiste gegevens 
    $naam = trim($_POST['gebruikers_naam'] ?? '');
    $wachtwoord = $_POST['wachtwoord'] ?? '';

    if ($naam === '' || $wachtwoord === '') {
        $errors[] = 'Vul gebruikersnaam en wachtwoord in.';
    } else {
        // Variable die de gegevens inlaad 
        $stmt = $pdo->prepare("SELECT user_id, gebruikers_naam, wachtwoord, toegang FROM gebruikers WHERE gebruikers_naam = ?");
        $stmt->execute([$naam]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($wachtwoord, $user['wachtwoord'])) {
            $errors[] = 'Onjuiste inloggegevens.';
        } else {
            // Zorgt er voor dat de wachtwoord gehasht is 
            if (password_needs_rehash($user['wachtwoord'], PASSWORD_DEFAULT)) {
                $newHash = password_hash($wachtwoord, PASSWORD_DEFAULT);
                $upd = $pdo->prepare("UPDATE users SET wachtwoord=? WHERE user_id=?");
                $upd->execute([$newHash, $user['user_id']]);
            }

            // Wat er precies wordt op geslagen in de tijdelijke server opslag 
            $_SESSION['user'] = [
                'id' => (int) $user['user_id'],
                'naam' => $user['gebruikers_naam'],
                'toegang' => $user['toegang'],
            ];

            // Als je inlogt wordt je naat dit gestuurd
            header('Location: ../cms/index.php');
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="nl">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CMS inloggen</title>
  <link rel="stylesheet" href="css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&display=swap" rel="stylesheet">
</head>

<body>
    <header>
        <nav>
            <img src="../img/logo.png" alt="ven_logo">
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li><a href="../index.php#carNewsContainer">Actueel auto nieuws</a></li>
                <li><a href="../contactPagina/index.php">Contact</a></li>
                <li><a href="../cms/index.php">CMS</a></li>
                <li><a href="../occasionsPagina/index.php">Auto zoeken</a></li>
            </ul>
        </nav>
    </header>
       </header>

    <main class="auth">
      <div class="auth-card">
        <h1>Inloggen</h1>
        <p class="subtle">Toegang tot het inhoud beheer systeem</p>
        <!-- als er nog ingelogt moet worden krijg je deze melding te zien -->
        <?php if ($msg === 'login_required'): ?>
          <div class="alert alert-warn">Log eerst in.</div>
        <?php endif; ?>

        <?php foreach ($errors as $e): ?>
          <div class="alert alert-error"><?php echo htmlspecialchars($e); ?></div>
        <?php endforeach; ?>
        <!-- Zorgt er voor dat de gegevens verstuurd worden onzichtbaar ipv via GET in de browser url & zorgt er voor dat de browser niet auto gegevens invult-->
        <form method="post" autocomplete="off">
          <div>
            <label class="form-label" for="gebruikers_naam">Gebruikersnaam</label>
            <input class="form-control" id="gebruikers_naam" name="gebruikers_naam" required>
          </div>

          <div>
            <label class="form-label" for="wachtwoord">Wachtwoord</label>
            <input class="form-control" id="wachtwoord" type="password" name="wachtwoord" required>
          </div>

          <button class="btn-primary" type="submit">Inloggen</button>

          <div class="auth-meta">
            <span>Wachtwoord vergeten? Neem contact op met de beheerder </span>
          </div>
        </form>
      </div>
    </main>
</body>

</html>