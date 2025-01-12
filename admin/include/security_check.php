<?php
// Sicherstellen, dass die Datei nicht direkt über den Browser aufgerufen wird
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
    // Wenn die Anfrage nicht von AJAX kommt, blockiere den Zugriff
    die('Zugriff verweigert');
}

// Dein Code folgt hier
echo json_encode(['status' => 'success', 'message' => 'Daten abgerufen']);
?>
