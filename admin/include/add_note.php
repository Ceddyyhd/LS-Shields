<?php
session_start();
include 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $note_type = $_POST['note_type'] ?? 'notiz';
    $note_content = $_POST['note_content'] ?? '';
    $author = $_SESSION['username'] ?? 'Unbekannt'; // Benutzername aus der Session holen

    if (!$user_id || empty($note_content)) {
        echo json_encode(['success' => false, 'message' => 'Alle Felder ausfüllen.']);
        exit;
    }

    $sql = "INSERT INTO notes (user_id, type, content, created_at, author) 
            VALUES (:user_id, :type, :content, NOW(), :author)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':user_id' => $user_id,
        ':type' => $note_type,
        ':content' => $note_content,
        ':author' => $author
    ]);

    echo json_encode([
        'success' => true,
        'data' => [
            'type' => $note_type,
            'content' => $note_content,
            'created_at' => date('Y-m-d H:i:s'),
            'user' => $author
        ]
    ]);
    exit;
}
