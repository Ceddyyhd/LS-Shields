<?php
define('ALLOW_ACCESS', true);

// Sicherheitsskript einbinden, um den Zugriff zu überprüfen
include 'security_check.php';

// get_users.php
require 'db.php'; // Deine DB-Verbindung

$sql = "SELECT id, name FROM users";
$result = mysqli_query($conn, $sql);
$users = [];

while ($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}

echo json_encode($users); // Benutzer als JSON zurückgeben
?>
