<?php
include 'db.php';

try {
    // Benutzer mit dem Status 'gekündigt' abrufen
    $sql = "SELECT users.*, roles.name AS role_name 
            FROM users 
            LEFT JOIN roles ON users.role_id = roles.id 
            WHERE users.gekuendigt = 'gekündigt'";  // Filter nur 'gekündigte' Benutzer

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // JSON-Ausgabe der Daten
    header('Content-Type: application/json');
    echo json_encode($users, JSON_PRETTY_PRINT);
    exit;

} catch (PDOException $e) {
    // Fehlerausgabe bei SQL-Problemen
    echo json_encode(['error' => 'SQL-Fehler: ' . $e->getMessage()]);
    exit;
}
