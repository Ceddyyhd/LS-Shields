<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'] ?? null;
    $note_type = $_POST['note_type'] ?? null;
    $note_content = $_POST['note_content'] ?? null;

    if (!$user_id || !$note_type || !$note_content) {
        echo json_encode(['success' => false, 'message' => 'Alle Felder sind erforderlich.']);
        exit;
    }

    try {
        $username = $_SESSION['username'] ?? 'Unbekannt';
    
        $stmt = $conn->prepare("INSERT INTO notes (user_id, type, content, created_by, created_at) 
                                VALUES (:user_id, :type, :content, :created_by, NOW())");
        $stmt->execute([
            ':user_id' => $user_id,
            ':type' => $note_type,
            ':content' => $note_content,
            ':created_by' => $username
        ]);
    
        $note_id = $conn->lastInsertId();
    
        echo json_encode([
            'success' => true,
            'data' => [
                'id' => $note_id,
                'user' => htmlspecialchars($username),
                'type' => $note_type,
                'content' => htmlspecialchars($note_content),
                'created_at' => date('Y-m-d H:i:s')
            ]
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Hinzufügen der Notiz.']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage.']);
