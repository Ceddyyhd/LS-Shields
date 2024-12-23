<?php
// Debugging aktivieren
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// JSON-Antwort sicherstellen
header('Content-Type: application/json');

// Session starten
session_start();

// Datenbankverbindung einbinden
include 'db.php';

try {
    // POST-Daten validieren
    $user_id = $_POST['user_id'] ?? null;
    $note_type = $_POST['note_type'] ?? null;
    $note_content = $_POST['note_content'] ?? null;

    if (!$user_id || !$note_type || !$note_content) {
        echo json_encode(['success' => false, 'message' => 'Alle Felder ausfüllen.']);
        exit;
    }

    // Benutzername aus der Session oder Datenbank holen
    $stmt_user = $conn->prepare("SELECT name FROM users WHERE id = :id");
    $stmt_user->execute([':id' => $user_id]);
    $user = $stmt_user->fetch(PDO::FETCH_ASSOC);
    $author = $user['name'] ?? 'Unbekannt';

    // Datenbankeintrag für die Notiz
    $stmt = $conn->prepare("INSERT INTO notes (user_id, note_type, content, created_at, author) 
                            VALUES (:user_id, :note_type, :content, NOW(), :author)");
    $stmt->execute([
        'user_id' => $user_id,
        'note_type' => $note_type,
        'content' => $note_content,
        'author' => $author
    ]);

    // Erfolg zurückgeben
    echo json_encode([
        'success' => true,
        'message' => 'Notiz hinzugefügt.',
        'data' => [
            'type' => $note_type,
            'content' => $note_content,
            'created_at' => date('Y-m-d H:i:s'),
            'user' => $author
        ]
    ]);
} catch (Exception $e) {
    // Fehler in der Logdatei speichern und zurückgeben
    file_put_contents('debug_add_note.log', $e->getMessage() . PHP_EOL, FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
    exit;
}
