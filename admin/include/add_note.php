<?php
include 'db.php';
session_start();
header('Content-Type: application/json');

// Überprüfen, ob die Anfrage per POST gesendet wurde
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage.']);
    exit;
}

// Eingabedaten prüfen
$user_id = $_POST['user_id'] ?? null;
$note_type = $_POST['note_type'] ?? null;
$note_content = $_POST['note_content'] ?? null;

if (!$user_id || !$note_type || !$note_content) {
    echo json_encode(['success' => false, 'message' => 'Alle Felder ausfüllen.']);
    exit;
}

// Berechtigung prüfen
$requiredPermission = '';
if ($note_type === 'notiz') {
    $requiredPermission = 'create_note';
} elseif ($note_type === 'verwarnung') {
    $requiredPermission = 'create_warning';
} elseif ($note_type === 'kuendigung') {
    $requiredPermission = 'create_termination';
}

if (!hasPermission($user_id, $requiredPermission, $conn)) {
    echo json_encode(['success' => false, 'message' => 'Keine Berechtigung für diese Aktion.']);
    exit;
}

// Benutzername des Autors ermitteln
$stmt = $conn->prepare("SELECT name FROM users WHERE id = :id");
$stmt->execute([':id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$author = $user['name'] ?? 'Unbekannt';

// Notiz in die Datenbank einfügen
try {
    $stmt = $conn->prepare("INSERT INTO notes (user_id, type, content, created_at, author) 
                            VALUES (:user_id, :type, :content, NOW(), :author)");
    $stmt->execute([
        'user_id' => $user_id,
        'type' => $note_type,
        'content' => $note_content,
        'author' => $author
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Notiz erfolgreich hinzugefügt.',
        'data' => [
            'type' => $note_type,
            'content' => $note_content,
            'created_at' => date('Y-m-d H:i:s'),
            'user' => $author
        ]
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
}
