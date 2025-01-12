<?php
// Überprüfen, ob die Anfrage vom richtigen Referer kommt (optional, aber hilfreich bei externen Anfragen)
if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'ls-shields.ceddyyhd2.eu') === false) {
    die('Zugriff verweigert');
}

// Dein Code folgt hier
echo json_encode(['status' => 'success', 'message' => 'Daten abgerufen']);
?>
