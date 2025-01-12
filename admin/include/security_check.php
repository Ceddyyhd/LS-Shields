<?php
// Prüfen, ob die Datei direkt aufgerufen wurde oder als include eingebunden wird
if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], 'ls-shields.ceddyyhd2.eu/admin/') === false) {
    // Wenn die Datei ohne die korrekte Referenz aufgerufen wurde und nicht eingebunden ist, Weiterleitung zu error.php
    if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {
        header('Location: ../error.php');
        exit();
    }
}
?>
