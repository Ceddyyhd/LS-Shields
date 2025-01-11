<?php
include 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header('Location: ../error.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $note_type = filter_input(INPUT_POST, 'note_type', FILTER_SANITIZE_STRING);
    $note_content = filter_input(INPUT_POST, 'note_content', FILTER_SANITIZE_STRING);

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

        // Loggen des Eintrags
        logAction('INSERT', 'notes', 'user_id: ' . $user_id . ', type: ' . $note_type);

        $note_id = $conn->lastInsertId();
        $stmt = $conn->prepare("SELECT * FROM notes WHERE id = :id");
        $stmt->execute([':id' => $note_id]);
        $new_note = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'data' => $new_note]);
    } catch (Exception $e) {
        error_log('Database error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern: ' . $e->getMessage()]);
    }
    exit;
} else {
    header('Location: ../error.php');
    exit;
}

// Funktion zum Loggen von Aktionen
function logAction($action, $table, $details) {
    global $conn;

    // SQL-Abfrage zum Einfügen des Log-Eintrags
    $stmt = $conn->prepare("INSERT INTO logs (action, table_name, details, user_id, timestamp) VALUES (:action, :table_name, :details, :user_id, NOW())");
    $stmt->bindParam(':action', $action, PDO::PARAM_STR);
    $stmt->bindParam(':table_name', $table, PDO::PARAM_STR);
    $stmt->bindParam(':details', $details, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
}
?>
