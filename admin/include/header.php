<?php
session_start();

// Geheimer Schlüssel zum Berechnen des privaten Tokens
define('SECRET_KEY', 'my_very_secret_key');

// Verbindung zur Datenbank herstellen
require_once 'include/db.php'; // Deine DB-Verbindung

// CSRF-Token generieren, falls noch nicht vorhanden
if (!isset($_SESSION['csrf_token_public'])) {
    $_SESSION['csrf_token_public'] = bin2hex(random_bytes(32)); // Öffentlicher Token
}

// Berechne den privaten Token (dieser wird NUR in der Session gespeichert)
$private_token = hash_hmac('sha256', $_SESSION['csrf_token_public'], SECRET_KEY);

// Speichern des privaten Tokens in der Session für den aktuellen Benutzer
$_SESSION['csrf_token_private'] = $private_token;

// Setzen des öffentlichen Tokens im Cookie für das Frontend
setcookie('csrf_token_public', $_SESSION['csrf_token_public'], [
    'expires' => time() + 3600, // Gültigkeit des Cookies (1 Stunde)
    'path' => '/',              // Cookie für die gesamte Domain verfügbar
    'secure' => true,           // Nur über HTTPS verfügbar
    'httponly' => false,        // JavaScript kann auf das Cookie zugreifen
    'samesite' => 'Strict'      // Schützt vor CSRF-Angriffen
]);
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