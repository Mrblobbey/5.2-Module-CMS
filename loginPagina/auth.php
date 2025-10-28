<?php
// Start session precies 1 keer
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Session is een tijdelijke opslag die gegevens bewaart op de server
function isLoggedIn(): bool {
  return !empty($_SESSION['user']);
}
    // De funtctie die zorgt dat de pagina beveiligd is met login 
function requireLogin($loginPaginaUrl): void {
  if (!isLoggedIn()) {
    header("Location: $loginPaginaUrl?msg=login_required");
    exit;
  }
}

