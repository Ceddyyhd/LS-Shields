<?php
// Fehlerprotokollierung aktivieren (für Debugging)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Datenbankverbindung einbinden
include 'db.php';

// Überprüfen, ob der Benutzer eingeloggt ist
session_start();

// Wenn der Benutzer nicht eingeloggt ist, dann nichts tun
if (!isset($_SESSION['user_id'])) {
    die("Kein Benutzer eingeloggt.");
}

header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header('Location: ../error.php');
    exit;
}

// Empfangen des Kategoriernamen
$new_category_name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);

// Überprüfen, ob der Name leer ist
if (empty($new_category_name)) {
    echo json_encode(['success' => false, 'message' => 'Kategorie Name darf nicht leer sein.']);
    exit;
}

try {
    // SQL-Abfrage, um die neue Kategorie hinzuzufügen
    $stmt = $conn->prepare("INSERT INTO ausruestungskategorien (name) VALUES (:name)");
    $stmt->execute([
        ':name' => $new_category_name
    ]);

    // Log-Eintrag für das Erstellen
    $category_id = $conn->lastInsertId();
    logAction('INSERT', 'ausruestungskategorien', 'category_id: ' . $category_id . ', created_by: ' . $_SESSION['user_id']);

    // Erfolgreiche Antwort zurückgeben
    echo json_encode(['success' => true, 'message' => 'Kategorie erfolgreich hinzugefügt.']);

} catch (PDOException $e) {
    // Fehlerbehandlung
    error_log('Fehler beim Hinzufügen der Kategorie: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Fehler beim Hinzufügen der Kategorie: ' . $e->getMessage()]);
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
