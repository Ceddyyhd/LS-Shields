<?php
include 'security_check.php'; // Sicherheitsprüfung für diese Datei

include 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $note_type = $_POST['note_type'] ?? null;
    $note_content = $_POST['note_content'] ?? null;

    if (!$user_id || !$note_type || !$note_content) {
        echo json_encode(['success' => false, 'message' => 'Alle Felder ausfüllen.']);
        exit;
    }

    // Autor aus der Session holen oder aus der Datenbank abrufen
    if (!isset($_SESSION['username'])) {
        $stmt = $conn->prepare("SELECT name FROM users WHERE id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['username'] = $user['name'] ?? 'Unbekannt';
    }

    $author = $_SESSION['username'];

    try {
        $stmt = $conn->prepare("INSERT INTO notes (user_id, type, content, created_at, author) 
                                VALUES (:user_id, :type, :content, NOW(), :author)");
        $stmt->execute([
            ':user_id' => $user_id,
            ':type' => $note_type,
            ':content' => $note_content,
            ':author' => $author,
        ]);

        $note_id = $conn->lastInsertId();
        $stmt = $conn->prepare("SELECT * FROM notes WHERE id = :id");
        $stmt->execute([':id' => $note_id]);
        $new_note = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'data' => $new_note]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern: ' . $e->getMessage()]);
    }
    exit;
}
?>
