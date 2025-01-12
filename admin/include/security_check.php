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

// Überprüfen, ob die Anfrage von /admin/ kommt (einschließlich Unterverzeichnissen wie /admin/include/)
$current_url = $_SERVER['REQUEST_URI'];

if (strpos($current_url, '/admin/') !== 0) {  // Wenn die URL nicht mit /admin/ beginnt
    header('Location: /error.php');
    exit;
}

// CSRF-Token-Überprüfung und andere Sicherheitsprüfungen
// Weiterer Code, wie du es benötigst...

ob_end_flush(); // Pufferung ausgeben, wenn keine Weiterleitung erfolgt
?>
