<?php
// Prüfen, ob die Datei direkt aufgerufen wurde
if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], '/include/') === false) {
    // Wenn die Datei ohne die korrekte Referenz aufgerufen wurde, Weiterleitung zu error.php
    header('Location: ../error.php');
    exit();
}
?>
