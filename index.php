<?php
// werkend maken voor de links 
$mainPath = './';

// standaardwaarden voor filters zodat ze werken en ingeladen worden 
$when = $_GET['when'] ?? 'all';   // all|week|today
$category = $_GET['category'] ?? 'all';   // all|new|soon
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AnnexBios - Home</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&display=swap" rel="stylesheet">
</head>

<body>

    <div class="background"></div>
    <div class="container">
        <?php
        require_once 'includes/header.php';
        // require_once 'includes/film_array.php';
        ?>
        <main>
            <section class="welkom">
                <div class="inhoud-container">
                    <h1>WELKOM BIJ ANNEXBIOS</h1>
                    <p class="welcome-text">Geniet van de nieuwste films in onze gezellige bioscoop</p>
                    <a href="<?php echo $mainPath ?>filmAgenda/filmagenda.php">BEKIJK DE DRAAIENDE FILMS</a>
                </div>
            </section>
        </main>

        <section class="location-section">
            <div class="inhoud-container">
                <div class="location-grid">
                    <div class="map-container">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2465.390030230544!2d4.1302237771906105!3d51.83557227188992!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c451c6f4434d53%3A0x20bb4b6bcdd57904!2sRijksstraatweg%2042%2C%203223%20KA%20Hellevoetsluis!5e0!3m2!1sen!2snl!4v1757508034573!5m2!1sen!2snl"
                            width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade" title="AnnexBios Location">
                        </iframe>
                    </div>
                    <div id="contact-info" class="contact-info">
                        <h2>Bezoek Ons</h2>
                        <address class="address-info">
                            <p class="address-line">Rijksstraatweg 42</p>
                            <p class="address-line">3223 KA Hellevoetsluis</p>
                            <p class="phone"><a href="tel:02012345678">020-12345678</a></p>
                            <p class="mail-address-line">support@AnnexBios.nl</p>
                        </address>

                        <div class="accessibility">
                            <h3>BEREIKBAARHEID</h3>
                            <p>Onze bioscoop is uitstekend bereikbaar met zowel het openbaar vervoer als met de auto. Er
                                is voldoende parkeergelegenheid in de buurt.</p>
                        </div>

                        <img src="photos/film.png" alt="Filmicoon" class="img_film">
                    </div>
                </div>
            </div>
        </section>

        <section class="filmagenda">
            <h2>FILM AGENDA</h2>

            <div class="filters">
                <!-- icon button -->
                <button class="icon-btn">
                    <img src="img/agenda.png" alt="Agenda icoon">
                </button>

                <form method="get" style="display:flex; gap:.5rem; align-items:center;">
                    <!-- bewaart huidige 'when' voor de dropdown-submit -->
                    <input type="hidden" name="when" value="<?= htmlspecialchars($when) ?>">

                    <!-- regular buttons -->
                    <button type="submit" name="when" value="all">FILMS</button>
                    <button type="submit" name="when" value="week">DEZE WEEK</button>
                    <button type="submit" name="when" value="today">VANDAAG</button>

                    <!-- dropdown -->
                    <select name="category" onchange="this.form.submit()">
                        <option value="all" <?= ($category === 'all' ? 'selected' : '') ?>>CATEGORIE</option>
                        <option value="all" <?= ($category === 'all' ? 'selected' : '') ?>>Alle films</option>
                        <option value="new" <?= ($category === 'new' ? 'selected' : '') ?>>Nieuwe films</option>
                        <option value="soon" <?= ($category === 'soon' ? 'selected' : '') ?>>Binnenkort</option>
                    </select>
                </form>

        </section>

        <?php
        // Huidige lijst als startpunt
        $filtered = $films;

        // Lees keuzes (of defaults)
        $when = $_GET['when'] ?? 'all';   // all|week|today
        $category = $_GET['category'] ?? 'all';   // all|new|soon
        
        // Handige tijdstempels
        $now = time();
        $todayStart = strtotime('today');
        $todayEnd = strtotime('tomorrow');
        $weekEnd = strtotime('+7 days', $todayStart);

        // WHEN-filter
        if ($when === 'today') {
            $filtered = array_filter(
                $filtered,
                fn($f) =>
                ($t = strtotime($f['start_time'] ?? '')) && $t >= $todayStart && $t < $todayEnd
            );
        } elseif ($when === 'week') {
            $filtered = array_filter(
                $filtered,
                fn($f) =>
                ($t = strtotime($f['start_time'] ?? '')) && $t >= $todayStart && $t < $weekEnd
            );
        }

        // CATEGORY-filter (
        if ($category === 'new') {
            // release_date of anders start_time binnen laatste 30 dagen
            $windowStart = strtotime('-30 days', $now);
            $filtered = array_filter(
                $filtered,
                fn($f) =>
                ($r = strtotime($f['movie']['release_date'] ?? $f['start_time'] ?? '')) && $r >= $windowStart && $r <= $now
            );
        } elseif ($category === 'soon') {
            // voorstellingen in de toekomst
            $filtered = array_filter(
                $filtered,
                fn($f) =>
                ($t = strtotime($f['start_time'] ?? $f['movie']['release_date'] ?? '')) && $t > $now
            );
        }

        // Altijd simpel sorteren op start_time
        usort($filtered, fn($a, $b) => strtotime($a['start_time'] ?? '') <=> strtotime($b['start_time'] ?? ''));
        ?>


    <section class="film_container">
            <?php foreach ($filtered as $film): ?>
                <div class="film_card">
                    <img src="https://image.tmdb.org/t/p/w500<?php echo $film['movie']['poster_path']; ?>"
                        alt="<?php echo $film['movie']['title']; ?>" class="film_afbeelding">

                    <div class="film_details">
                        <h3><?php echo $film['movie']['title']; ?></h3>

                        <div class="film_rating" >
                                    <?php
                                    $rating = (float) explode('/', $film['movie']['vote_average'])[0];
                                    $stars = round($rating / 2); // Convert to 1-5 scale
                                
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $stars) {
                                            echo '★';
                                        } else {
                                            echo '☆';
                                        }
                                    }
                                    ?>
                                </div>
                        
                                <p class=" release_date">Release: <?php echo $film['movie']['release_date']; ?></p>
                            <p class="film_description"><?php echo $film['movie']['overview']; ?></p>
                        </div>

                        <div class="button">
                            <a href="stoelpagina/stoelpagina.php?id=<?php echo $film['id'] ?>">MEER INFO &amp; TICKETS</a>
                        </div>
                    </div>
                <?php endforeach; ?>
        </section>
    </div>
    </body>
    </main>