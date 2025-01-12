<?php
// Prüfen, ob die Anfrage vom richtigen Verzeichnis kommt (z.B. '/admin/')
$allowed_admin_prefix = '/admin/';  // Erlaubte URL-Präfix für Admin
$current_url = $_SERVER['REQUEST_URI']; // Holt die aktuelle URL

// Überprüfen, ob die Anfrage von der richtigen Quelle kommt
if (strpos($current_url, $allowed_admin_prefix) !== 0) {
    // Wenn die URL nicht mit '/admin/' beginnt, blockiere die Anfrage und leite weiter
    header('Location: /error.php');
    exit;
}
?>
