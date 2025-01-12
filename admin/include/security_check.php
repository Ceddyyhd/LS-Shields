<?php 
$allowed_domain = 'ls-shields.ceddyyhd2.eu';
$allowed_methods = ['GET', 'POST'];  // Hier definierst du, welche HTTP-Methoden erlaubt sind

$host = $_SERVER['HTTP_HOST'];  // Holt den Host der aktuellen Anfrage
$request_method = $_SERVER['REQUEST_METHOD'];  // Holt die HTTP-Methode der Anfrage

// Sicherstellen, dass die Anfrage von der erlaubten Domain kommt und die Methode erlaubt ist
if (strpos($host, $allowed_domain) === false || !in_array($request_method, $allowed_methods)) {
    // Falls die Anfrage nicht von der erlaubten Domain oder Methode kommt, Weiterleitung zur Fehlerseite
    header('Location: ../error.php');
    exit;
}

?>