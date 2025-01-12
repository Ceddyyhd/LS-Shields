<?php
session_start();

// CSRF-Token aus dem HTTP-Only Cookie holen
$csrf_token = isset($_COOKIE['csrf_token']) ? $_COOKIE['csrf_token'] : '';

// Wenn der Token vorhanden ist, gib ihn zurück, andernfalls einen Fehler
if ($csrf_token) {
    echo json_encode(['success' => true, 'csrf_token' => $csrf_token]);
} else {
    echo json_encode(['success' => false, 'message' => 'CSRF Token nicht gefunden']);
}
?>
