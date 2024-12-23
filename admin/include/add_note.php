<?php
session_start();
include 'db.php';
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'] ?? null; // ID des eingeloggten Benutzers
    $note_type = $_POST['note_type'] ?? null;
    $note_content = $_POST['note_content'] ?? null;

    if (!$user_id || !$note_type || !$note_content) {
        echo json_encode(['success' => false, 'message' => 'Alle Felder müssen ausgefüllt sein.']);
        exit;
    }

    try {
        $stmt = $conn->prepare("INSERT INTO notes (user_id, note_type, note_content, created_at) VALUES (:user_id, :note_type, :note_content, NOW())");
        $stmt->execute([
            ':user_id' => $user_id,
            ':note_type' => $note_type,
            ':note_content' => $note_content
        ]);

        // Erfolgreiche Antwort mit den Daten der Notiz
        echo json_encode([
            'success' => true,
            'data' => [
                'user' => $_SESSION['username'], // Angemeldeter Benutzername
                'content' => htmlspecialchars($note_content),
                'created_at' => date('Y-m-d H:i:s') // Aktuelles Datum/Zeit
            ]
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern der Notiz.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage.']);
}
