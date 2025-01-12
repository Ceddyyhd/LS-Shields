<?php
session_start();

// Überprüfe, ob es sich um eine AJAX-Anfrage handelt
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
    // Überprüfe den Token in der Anfrage für AJAX
    if (!isset($_SERVER['HTTP_X_AJAX_TOKEN']) || $_SERVER['HTTP_X_AJAX_TOKEN'] !== $_SESSION['ajax_token']) {
        // Wenn das Token nicht stimmt, den Zugriff verweigern
        header('HTTP/1.1 403 Forbidden');
        echo json_encode(['status' => 'error', 'message' => 'Ungültiges Token']);
        exit;
    }
}

// Deine Logik, um die Anfrage zu verarbeiten
echo json_encode(['status' => 'success', 'message' => 'Daten abgerufen']);
?>
