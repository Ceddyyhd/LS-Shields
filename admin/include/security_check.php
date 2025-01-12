<?php
// Sicherstellen, dass die Datei nicht direkt aufgerufen wird
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Zugriff verweigert'); // Blockiert den direkten Aufruf
}

// Der Code folgt hier
echo json_encode(['status' => 'success', 'message' => 'Daten abgerufen']);
?>
