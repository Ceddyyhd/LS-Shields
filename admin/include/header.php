





<?php
session_start();

// Generiere ein Token, wenn es nicht schon existiert
if (!isset($_SESSION['ajax_token'])) {
    $_SESSION['ajax_token'] = bin2hex(random_bytes(32));  // Beispiel: Ein 32-Byte zufälliges Token
}

// Gib das Token an JavaScript aus
echo "<script>var ajaxToken = '" . $_SESSION['ajax_token'] . "';</script>";
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
