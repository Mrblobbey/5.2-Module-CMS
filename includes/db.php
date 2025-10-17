
<?php
$host = 'localhost';            // of je servernaam
$db   = 'van_der_ven_autos';              // ✅ dit moet exact de naam van je database zijn
$user = 'root';                 // of jouw gebruikersnaam
$pass = '';                     // moet veranderder worden ivm met onveilig php my admin 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database verbinding mislukt: " . $e->getMessage());
}
?>