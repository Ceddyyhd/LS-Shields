<?php
session_start();

// Überprüfe den Token in der Anfrage
if (!isset($_SERVER['HTTP_X_AJAX_TOKEN']) || $_SERVER['HTTP_X_AJAX_TOKEN'] !== $_SESSION['ajax_token']) {
    // Wenn das Token nicht stimmt, den Zugriff verweigern
    die('Zugriff verweigert');
}

// Hier kommt der Code, der die Anfrage verarbeitet
echo json_encode(['status' => 'success', 'message' => 'Daten abgerufen']);
?>
