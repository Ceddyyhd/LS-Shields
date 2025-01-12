<?php
session_start();

// CSRF-Token generieren, falls noch nicht vorhanden
if (!isset($_SESSION['csrf_token_private'])) {
    $_SESSION['csrf_token_private'] = bin2hex(random_bytes(32)); // Generiere sicheren privaten CSRF-Token
}

if (!isset($_SESSION['csrf_token_public'])) {
    $_SESSION['csrf_token_public'] = bin2hex(random_bytes(32)); // Generiere sicheren öffentlichen CSRF-Token
}

// Verbinde dich mit der Datenbank, um den privaten Token zu speichern
// Einbinden der DB-Verbindung
require_once 'db.php';  // Hier wird die DB-Verbindung aus der db.php geladen

try {
    // Speichere den privaten CSRF-Token in der Datenbank (dieser wird sicher und nur dort gespeichert)
    $stmt = $pdo->prepare("UPDATE users SET csrf_token_private = :csrf_token WHERE user_id = :user_id");
    $stmt->execute([
        ':csrf_token' => $_SESSION['csrf_token_private'],
        ':user_id' => $_SESSION['user_id'] // Achte darauf, dass die User-ID korrekt ist
    ]);

} catch (PDOException $e) {
    echo "Datenbankfehler: " . $e->getMessage();
    exit;
}

// Setze den öffentlichen Token als Cookie (für die Verwendung im Frontend)
setcookie('csrf_token_public', $_SESSION['csrf_token_public'], [
    'expires' => time() + 3600,  // Cookie gültig für 1 Stunde
    'path' => '/',               // Cookie für die gesamte Domain verfügbar
    'secure' => true,            // Nur über HTTPS verfügbar
    'httponly' => false,         // JavaScript kann auf das Cookie zugreifen
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