<?php
// Sicherstellen, dass die Datei nicht direkt über die URL aufgerufen wird
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Zugriff verweigert'); // Blockiere den Zugriff, wenn die Datei direkt aufgerufen wird
}

// Alternativ könnte man hier auch eine Prüfung auf Referer oder Session machen

// Der eigentliche Code folgt hier
?>