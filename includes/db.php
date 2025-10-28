<?php
$host = 'localhost';            // of je servernaam
$db   = 'van_der_ven_autos';    // naam van de DataBase 
$user = 'root';                 // gebruikersnaam
$pass = '';                     // Wachtwoord van de db

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database verbinding mislukt: " . $e->getMessage());
}
?>

<!-- 
-- SELECT  → gegevens opvragen
SELECT * FROM autos;

-- INSERT  → nieuwe rij toevoegen
INSERT INTO autos (merk, model, bouwjaar, prijs)
VALUES ('Tesla', 'Model 3', 2020, 38999);

-- UPDATE  → bestaande gegevens wijzigen
UPDATE autos
SET prijs = 36999
WHERE merk = 'Tesla' AND model = 'Model 3';

-- DELETE  → rijen verwijderen
DELETE FROM autos
WHERE merk = 'Volkswagen' AND bouwjaar < 2010;

-- DROP  → hele tabel verwijderen
DROP TABLE autos;

-->