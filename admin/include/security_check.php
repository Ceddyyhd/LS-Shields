<?php
// Sicherstellen, dass die Anfrage aus der richtigen Quelle kommt (z. B. der eigenen Domain)
if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'ls-shields.ceddyyhd2.eu') === false) {
    die('Zugriff verweigert');  // Verhindert den Zugriff, wenn der Referer nicht korrekt ist
}

// Dein Code folgt hier
echo json_encode(['status' => 'success', 'message' => 'Daten abgerufen']);
?>
