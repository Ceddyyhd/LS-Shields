<?php
// Prüfen, ob die Datei direkt aufgerufen wurde
if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {
    // Wenn die Datei direkt aufgerufen wird und nicht über ein include
    if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], '/admin/') === false) {
        die('Zugriff verweigert');
    }
}
?>
