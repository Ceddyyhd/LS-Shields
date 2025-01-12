<?php 
// Überprüfen, ob die Anfrage von der richtigen Quelle kommt (ls-shields.ceddyyhd2.eu)
$allowed_domain = 'ls-shields.ceddyyhd2.eu';
$host = $_SERVER['HTTP_HOST']; // Holt den Host der aktuellen Anfrage

// Sicherstellen, dass die Anfrage von der erlaubten Domain kommt
if (strpos($host, $allowed_domain) === false) {
    // Falls die Anfrage nicht von der erlaubten Domain kommt, Weiterleitung zur Fehlerseite
    header('Location: ../error.php');
    exit;
}
?>