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
    $stmt = $conn->prepare("UPDATE kunden SET remember_token = NULL WHERE id = :id");
    $stmt->execute([':id' => $_SESSION['user_id']]);

    // Log-Eintrag für den Logout
    logAction('LOGOUT', 'kunden', 'Benutzer ausgeloggt: ID: ' . $_SESSION['user_id']);
}

// Session zerstören
session_destroy();

// Benutzer weiterleiten
header('Location: https://ls-shields.ceddyyhd2.eu/');
exit;

// Funktion zum Loggen von Aktionen
function logAction($action, $table, $details) {
    global $conn;

    // SQL-Abfrage zum Einfügen des Log-Eintrags
    $stmt = $conn->prepare("INSERT INTO logs (action, table_name, details, user_id, timestamp) VALUES (:action, :table_name, :details, :user_id, NOW())");
    $stmt->bindParam(':action', $action, PDO::PARAM_STR);
    $stmt->bindParam(':table_name', $table, PDO::PARAM_STR);
    $stmt->bindParam(':details', $details, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
}
?>
