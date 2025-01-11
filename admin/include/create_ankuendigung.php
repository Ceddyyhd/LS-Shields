<?php
include 'db.php';
session_start();
header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header('Location: ../error.php');
    exit;
}

// Eingabewerte
$key_name = filter_input(INPUT_POST, 'key_name', FILTER_SANITIZE_STRING);
$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
$prioritaet = filter_input(INPUT_POST, 'prioritaet', FILTER_VALIDATE_INT);
$created_by = $_POST['created_by'] ?? $_SESSION['username'];  // Benutzernamen aus der Session holen oder den angegebenen Namen aus dem Formular

if (!$key_name || !$description || !$prioritaet) {
    echo json_encode(['success' => false, 'error' => 'Fehlende Eingabewerte']);
    exit;
}

try {
    // Ankündigung erstellen
    $stmt = $conn->prepare("INSERT INTO ankuendigung (key_name, display_name, description, prioritaet, created_by) VALUES (:key_name, :key_name, :description, :prioritaet, :created_by)");
    $stmt->execute([
        ':key_name' => $key_name,
        ':display_name' => $key_name, // display_name ist in diesem Fall gleich key_name
        ':description' => $description,
        ':prioritaet' => $prioritaet,
        ':created_by' => $created_by  // Setze den Ersteller
    ]);

    // Log-Eintrag für das Erstellen
    $ankuendigung_id = $conn->lastInsertId();
    logAction('INSERT', 'ankuendigung', 'ankuendigung_id: ' . $ankuendigung_id . ', created_by: ' . $created_by);

    echo json_encode(['success' => true, 'message' => 'Ankündigung erfolgreich erstellt']);
} catch (PDOException $e) {
    error_log('Fehler beim Erstellen der Ankündigung: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Fehler beim Erstellen der Ankündigung: ' . $e->getMessage()]);
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
