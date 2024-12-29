<?php
session_start(); // Session starten

// Alle Session-Daten löschen
$_SESSION = [];

// Session-Cookie löschen
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}

// "Remember Me"-Cookie löschen
if (isset($_COOKIE['remember_me'])) {
    setcookie('remember_me', '', time() - 3600, '/'); // Cookie löschen
}

// Optional: Token aus der Datenbank löschen
include 'db.php';
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("UPDATE users SET remember_token = NULL WHERE id = :id");
    $stmt->execute([':id' => $_SESSION['user_id']]);
}

// Session zerstören
session_destroy();

// Benutzer weiterleiten
header('Location: https://ls-shields.ceddyyhd2.eu/');
exit;
