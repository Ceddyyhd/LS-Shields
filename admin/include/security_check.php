<?php
ob_start(); // Ausgabe-Pufferung starten

session_start();

// Überprüfen, ob es eine POST- oder GET-Anfrage ist
$allowed_methods = ['GET', 'POST'];  // Nur GET und POST erlauben
$request_method = strtoupper($_SERVER['REQUEST_METHOD']); // Methode der Anfrage in Großbuchstaben

// Nur POST oder GET werden akzeptiert
if (!in_array($request_method, $allowed_methods)) {
    header('Location: /error.php');
    exit;
}

// Wenn es eine GET-Anfrage ist, prüfen, ob sie aus einer erlaubten Quelle kommt
if ($request_method === 'GET') {
    $allowed_get_urls = [
        '/admin/', // Hier fügst du URLs hinzu, die von GET-Anfragen zugelassen werden sollen
    ];
    $current_url = $_SERVER['REQUEST_URI'];

    // Wenn die URL nicht in den erlaubten URLs enthalten ist, zeige eine Fehlerseite
    if (!in_array($current_url, $allowed_get_urls)) {
        header('Location: /error.php');
        exit;
    }
}

// CSRF-Token-Überprüfung und andere Sicherheitsprüfungen
// Weiterer Code, wie du es benötigst...

ob_end_flush(); // Pufferung ausgeben, wenn keine Weiterleitung erfolgt
?>
