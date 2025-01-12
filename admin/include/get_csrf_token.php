<?php
session_start();

// Stelle sicher, dass der Benutzer authentifiziert ist, bevor du den CSRF-Token zurückgibst
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Nicht autorisiert']);
    exit;
}

// CSRF-Token abrufen und zurückgeben
echo json_encode(['success' => true, 'csrf_token' => $_SESSION['csrf_token']]);
?>