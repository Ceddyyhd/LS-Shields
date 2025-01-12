<?php
session_start();

// Überprüfe, ob die URL direkt auf eine Datei im 'admin/include/' verweist
if (strpos($_SERVER['REQUEST_URI'], '/admin/include/') !== false) {
    die('Zugriff verweigert');
}

// Deine eigentliche Logik für die Anfrage
echo json_encode(['status' => 'success', 'message' => 'Daten abgerufen']);
?>
