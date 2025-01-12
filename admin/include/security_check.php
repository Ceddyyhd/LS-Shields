<?php
// Überprüfen, ob die Datei über ein 'include' oder 'require' eingebunden wurde
if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {
    die('Zugriff verweigert');
}
?>