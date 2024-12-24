<?php
define('SECURE_ACCESS', true);
require_once 'include/db.php';

// Nur autorisierte Anfragen zulassen (optional)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['error' => 'Ungültige Anfrage']));
}

// Mitarbeiterdaten sicher abrufen
try {
    $stmt = $conn->prepare("
        SELECT 
            u.id,
            u.name,
            u.nummer,
            u.created_at,
            r.name AS role_name
        FROM users u
        LEFT JOIN roles r ON u.role_id = r.id
    ");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // JSON-Antwort
    echo json_encode($users);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Fehler beim Abrufen der Daten']);
}
?>
