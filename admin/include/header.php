<?php
session_start();

// Generiere den CSRF-Token, wenn er noch nicht existiert
if (!isset($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));  // Generiere einen sicheren CSRF-Token
}

// In header.php: CSRF-Token im Cookie setzen
setcookie('csrf_token', $_SESSION['csrf_token'], [
  'expires' => time() + 3600,
  'path' => '/',
  'secure' => true,
  'httponly' => true,
  'samesite' => 'None'  // Erlaubt den Cookie, bei Cross-Domain-Anfragen gesendet zu werden
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