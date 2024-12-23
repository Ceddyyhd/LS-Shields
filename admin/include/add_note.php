<?php
session_start();
include 'db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $note_type = $_POST['note_type'] ?? 'notiz';
    $note_content = $_POST['note_content'] ?? '';

    if (!$user_id || empty($note_content)) {
        echo json_encode(['success' => false, 'message' => 'Alle Felder ausfüllen.']);
        exit;
    }

    // Benutzername ermitteln
    $author = $_SESSION['username'] ?? null;

    if (!$author) {
        // Falls die Session keinen Benutzernamen enthält, aus der Datenbank holen
        $stmt = $conn->prepare("SELECT name FROM users WHERE id = :id");
        $stmt->execute([':id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $author = $user['name'] ?? 'Unbekannt';
    }

    // Notiz in die Datenbank einfügen
    $stmt = $conn->prepare("INSERT INTO notes (user_id, note_type, content, created_at, author) 
                            VALUES (:user_id, :note_type, :content, NOW(), :author)");
    $stmt->execute([
        'user_id' => $user_id,
        'note_type' => $note_type,
        'content' => $note_content,
        'author' => $author,
    ]);

    // Rückmeldung mit den neuen Notizdaten
    $response = [
        'success' => true,
        'data' => [
            'type' => $note_type,
            'content' => $note_content,
            'created_at' => date('Y-m-d H:i:s'), // Aktuelle Zeit
            'user' => $author, // Name des Autors
        ],
    ];

    echo json_encode($response);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage.']);
