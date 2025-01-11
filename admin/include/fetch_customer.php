<?php
require_once 'db.php';

// Fehleranzeige für Debugging aktivieren
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'error' => 'Ungültiges CSRF-Token']);
    exit;
}

try {
    // Dein SQL-Statement für die Benutzerabfrage
    $stmt = $conn->prepare("
        SELECT 
            k.id,
            k.name,
            k.nummer,
            k.created_at
        FROM kunden k
        WHERE k.gekuendigt = 'no_kuendigung';
    ");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Wenn keine Benutzer gefunden wurden, gib ein leeres Array zurück
    if (empty($users)) {
        echo json_encode([]);
    } else {
        echo json_encode($users, JSON_PRETTY_PRINT);
    }
    exit;

} catch (PDOException $e) {
    // Fehlerausgabe bei SQL-Problemen
    error_log('SQL-Fehler: ' . $e->getMessage());
    echo json_encode(['error' => 'SQL-Fehler: ' . $e->getMessage()]);
    exit;
}
?>
