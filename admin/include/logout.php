<?php
session_start(); // Session starten

// Alle Session-Daten löschen
$_SESSION = [];

// Session-Cookie löschen
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}

// Session zerstören
session_destroy();

// Benutzer weiterleiten
header('Location: https://ls-shields.ceddyyhd2.eu/');
exit;
