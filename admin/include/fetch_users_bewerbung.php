<?php
include 'db.php';
session_start();
header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'error' => 'Ungültiges CSRF-Token']);
    exit;
}

try {
    // Benutzer mit dem Status 'bewerber' abrufen
    $sql = "SELECT users.*, roles.name AS role_name 
            FROM users 
            LEFT JOIN roles ON users.role_id = roles.id 
            WHERE users.bewerber = 'ja'";  // Filter nur 'bewerber' Benutzer

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // JSON-Ausgabe der Daten
    echo json_encode($users, JSON_PRETTY_PRINT);
    exit;

} catch (PDOException $e) {
    // Fehlerausgabe bei SQL-Problemen
    error_log('SQL-Fehler: ' . $e->getMessage());
    echo json_encode(['error' => 'SQL-Fehler: ' . $e->getMessage()]);
    exit;
}
?>
