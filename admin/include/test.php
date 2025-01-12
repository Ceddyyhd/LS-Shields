<?php
// Prüfen, ob die Datei direkt aufgerufen wurde
if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], '/admin/') === false) {
    // Wenn die Datei ohne die korrekte Referenz aufgerufen wurde
    die('Zugriff verweigert');
}
?>
