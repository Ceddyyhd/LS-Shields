<?php
session_start();

// CSRF-Token generieren, falls noch nicht vorhanden
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Erzeuge zufälligen Token
}

// Setze den CSRF-Token als HTTP-Only Cookie
setcookie('csrf_token', $_SESSION['csrf_token'], [
    'expires' => time() + 3600,  // Cookie gültig für 1 Stunde
    'path' => '/',               // Cookie für die gesamte Domain verfügbar
    'secure' => true,            // Nur über HTTPS verfügbar
    'httponly' => true,          // JavaScript kann den Cookie nicht lesen
    'samesite' => 'Strict'       // Schützt vor CSRF-Angriffen
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