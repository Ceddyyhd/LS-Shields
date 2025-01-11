<?php
include 'db.php';
session_start();
header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'error' => 'Ungültiges CSRF-Token']);
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

try {
    if ($id) {
        // Fetch für einen bestimmten Rabatt
        $stmt = $conn->prepare("SELECT id, display_name, description, rabatt_percent FROM rabatt WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $rabatt = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($rabatt) {
            echo json_encode([$rabatt]); // Erfolgreiches Abrufen der Rabatt-Daten
        } else {
            echo json_encode(['error' => 'Rabatt nicht gefunden']);
        }
    } else {
        // Fetch für alle Rabatte
        $stmt = $conn->prepare("SELECT id, display_name, description, rabatt_percent FROM rabatt");
        $stmt->execute();
        $rabatte = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($rabatte); // Erfolgreiches Abrufen aller Rabatte
    }
} catch (PDOException $e) {
    error_log('Fehler beim Abrufen der Rabatte: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Fehler beim Abrufen der Rabatte: ' . $e->getMessage()]);
}
?>
