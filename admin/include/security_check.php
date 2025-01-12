<?php
// Prüfen, ob die Datei direkt aufgerufen wurde
// Wenn der Referer leer ist oder der Referer nicht mit '/admin/' übereinstimmt, blockiere den Zugriff
if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], '/admin/') === false) {
    // Falls der Benutzer direkt die Seite aufruft (z. B. durch direkte URL-Eingabe)
    // Überprüfen, ob die aktuelle Anfrage auf die Admin-Seite geht
    if (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) {
        // Wenn der Benutzer direkt auf '/admin/' zugreift, erlaube den Zugriff
        // (z. B. wenn er direkt die Admin-Seite aufruft)
    } else {
        // Weiterleitung zur Fehlerseite, wenn keine gültige Referenz vorliegt
        header('Location: ../error.php');
        exit();
    }
}
?>
