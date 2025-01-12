<?php
session_start();

// Geheimer Schlüssel (geheim auf dem Server gespeichert)
define('SECRET_KEY', 'my_very_secret_key');

// Datenbankverbindung einbinden
require_once 'include/db.php'; // Stelle sicher, dass der Pfad zur db.php korrekt ist

// CSRF-Token generieren, falls noch nicht vorhanden
if (!isset($_SESSION['csrf_token_public'])) {
    $_SESSION['csrf_token_public'] = bin2hex(random_bytes(32)); // Erzeuge sicheren öffentlichen Token
}

// Berechne den privaten Token (dieser wird nicht im Cookie gespeichert, nur auf dem Server)
$private_token = hash_hmac('sha256', $_SESSION['csrf_token_public'], SECRET_KEY);

// Speichern des privaten Tokens in der Datenbank (nur sicher auf dem Server)
try {
    // Update des privaten Tokens in der Datenbank für den aktuellen Benutzer
    $stmt = $conn->prepare("UPDATE users SET csrf_token_private = :csrf_token WHERE id = :user_id");
    $stmt->execute([
        ':csrf_token' => $private_token,  // Speichere den privaten Token
        ':user_id' => $_SESSION['user_id']  // Die User-ID
    ]);
} catch (PDOException $e) {
    echo "Datenbankfehler: " . $e->getMessage();
    exit;
}

// Setzen des öffentlichen Tokens im Cookie
setcookie('csrf_token_public', $_SESSION['csrf_token_public'], [
    'expires' => time() + 3600, // Cookie läuft in einer Stunde ab
    'path' => '/',              // Cookie für die gesamte Domain verfügbar
    'secure' => true,           // Nur über HTTPS verfügbar
    'httponly' => false,        // JavaScript kann auf das Cookie zugreifen
    'samesite' => 'Strict'      // Schützt vor CSRF-Angriffen
]);

// Optional: Bestätigung oder Debugging
// echo "CSRF-Tokens wurden erfolgreich generiert und gespeichert.";
?>





<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>LS-Shields | Mitarbeiterverwaltung</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css?v=<?= time(); ?>">
</head>
<script src="include/security.js"></script>