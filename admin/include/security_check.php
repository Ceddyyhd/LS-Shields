<?php
// Sicherstellen, dass die Datei nicht direkt über den Browser aufgerufen wird
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
    die('Zugriff verweigert');
}

// Dein Code folgt hier
?>
