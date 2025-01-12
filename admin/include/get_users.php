<?php

// Definiere die Konstante, um den Zugriff auf die "include" Dateien zu erlauben
define('INCLUDE_SCRIPT', true);

// Die Datei im "include"-Ordner wird jetzt sicher eingebunden
include 'security_check.php';

// Der Code in der Datei wird jetzt sicher ausgeführt

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
