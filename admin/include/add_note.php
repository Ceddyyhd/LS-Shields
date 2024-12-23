<?php
session_start();
include 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'] ?? null; // Der aktuell angemeldete Benutzer
    $note_type = $_POST['note_type'] ?? 'notiz';
    $note_content = $_POST['note_content'] ?? '';

    if (!$user_id || empty($note_content)) {
        echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage.']);
        exit;
    }

    try {
        $stmt = $conn->prepare("INSERT INTO notes (user_id, type, content, created_at, author) VALUES (:user_id, :type, :content, NOW(), :author)");
        $stmt->execute([
            'user_id' => $user_id,
            'type' => $note_type,
            'content' => $note_content,
            'author' => $_SESSION['username'] ?? 'Unbekannt'
        ]);

        echo json_encode([
            'success' => true,
            'data' => [
                'type' => $note_type,
                'content' => $note_content,
                'created_at' => date('Y-m-d H:i:s'),
                'author' => $_SESSION['username'] ?? 'Unbekannt'
            ]
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern der Notiz.']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage.']);
