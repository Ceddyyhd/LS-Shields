<?php
// Sicherstellen, dass die Datei nur über AJAX oder PHP-Includes aufgerufen wird
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
    // Wenn die Anfrage nicht von AJAX kommt (kein `X-Requested-With`-Header), blockiere den Zugriff
    die('Zugriff verweigert');
}

// Dein Code folgt hier
echo json_encode(['status' => 'success', 'message' => 'Daten abgerufen']);
?>
