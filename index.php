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
    <!-- hmlt 5 header -->
    <header>
        <!-- navigatie en html 5 -->
        <nav>
            <img src="" alt="ven_logo">
            <ul>
                <li><a href="">Home</a></li>
                <li><a href="">Occasions</a></li>
                <li><a href="">Actueel auto nieuws</a></li>
                <li><a href="">Contact</a></li>
                <li><a href="">Auto zoeken</a></li>
            </ul>
        </nav>
    </header>
    <!-- html 5 main & belangrijk inhoud -->
    <main>
        <!-- hero achtergrond video -->
        <section class="hero">
            <div class="heroVideoWrapper">
                <iframe id="heroVideo"></iframe>
                <!-- donkere filter voor de video - -->
                <div class="heroOverlay"></div>
            </div>
            <!--  hero welkoms tekst & knoppen -->
            <div class="heroContent">
                <h1>WELKOM BIJ</h1>
                <h1>VENAUTO.NL</h1>
                <p>3885 klanten beoordeelden Van der Ven</p>
                <p>Autos met gemiddeld een 9,3</p>
                <!-- knoppen -->
                <button type="button">Auto zoeken</button>
                <button type="button">Contact</button>
            </div>
        </section>
        <!-- filter voor de producten -->
        <section class="filterContainer">
            <h2>Ons aanbod filter</h2>
            <div class="filterCategorie">
                <!-- dropdown merk -->
                <label for="brand">Merk:</label>
                <select id="brand" name="Merk">
                    <option value="tesla">Tesla</option>
                    <option value="volkswagen">Volkswagen</option>
                    <option value="audi">Audi</option>
                </select>
                <!-- dropdown model -->
                <label for="model">Model:</label>
                <select id="model" name="Model">
                    <option value="tesla">Tesla</option>
                    <option value="volkswagen">Volkswagen</option>
                    <option value="audi">Audi</option>
                </select>
                <!-- dropdown carroserie -->
                <label for="model">Model:</label>
                <select id="model" name="Model">
                    <option value="tesla">Tesla</option>
                    <option value="volkswagen">Volkswagen</option>
                    <option value="audi">Audi</option>
                </select>
                <!-- dropdown Brandstof -->
                <label for="model">Model:</label>
                <select id="model" name="Model">
                    <option value="tesla">Tesla</option>
                    <option value="volkswagen">Volkswagen</option>
                    <option value="audi">Audi</option>
                </select>
                <!-- dropdown Bouwjaar -->
                <label for="model">Model:</label>
                <select id="model" name="Model">
                    <option value="tesla">Tesla</option>
                    <option value="volkswagen">Volkswagen</option>
                    <option value="audi">Audi</option>
                </select>
                <!-- dropdown prijs -->
                <label for="model">Model:</label>
                <select id="model" name="Model">
                    <option value="tesla">Tesla</option>
                    <option value="volkswagen">Volkswagen</option>
                    <option value="audi">Audi</option>
                </select>
                <!-- km standen keuze knop  -->
                <label for="model">Model:</label>
                <select id="model" name="Model">
                    <option value="tesla">Tesla</option>
                    <option value="volkswagen">Volkswagen</option>
                    <option value="audi">Audi</option>
                </select>
                <!-- filter activeer knop -->
                 <button type="button">Vinden (1000) </button>
            </div>
        </section>
        <!-- Actueel auto nieuws -->
         <section id="carNewsContainer">
            
         </section>
    </main>

    <!-- footer html 5 -->
    <footer>
        <p>Nieuwsbrief aanmelden</p>
        <label for="text">Your mail</label>
        <input type="text">
        <button type="button">Subscribe </button>
    </footer>
</body>