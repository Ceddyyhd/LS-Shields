<?php
// User-Agent-Informationen auslesen
$user_agent = $_SERVER['HTTP_USER_AGENT'];

// Browser erkennen (optional, hier ein einfacher Vergleich)
if (strpos($user_agent, 'Chrome') !== false) {
    $browser = 'Google Chrome';
} elseif (strpos($user_agent, 'Firefox') !== false) {
    $browser = 'Mozilla Firefox';
} elseif (strpos($user_agent, 'Safari') !== false) {
    $browser = 'Apple Safari';
} elseif (strpos($user_agent, 'Edge') !== false) {
    $browser = 'Microsoft Edge';
} elseif (strpos($user_agent, 'MSIE') !== false || strpos($user_agent, 'Trident') !== false) {
    $browser = 'Internet Explorer';
} else {
    $browser = 'Unbekannter Browser';
}

// Ausgabe des Browsers
echo "Der Benutzer verwendet den Browser: " . $browser ." ";
echo $user_agent;
?>
