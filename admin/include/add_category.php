<?php
session_start();
require_once 'db.php';

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header('Location: ../error.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newCategoryName = filter_input(INPUT_POST, 'new_category_name', FILTER_SANITIZE_STRING);

    if (empty($newCategoryName)) {
        echo json_encode(['success' => false, 'message' => 'Kategorie-Name darf nicht leer sein.']);
        exit;
    }

    // Verhindern, dass eine doppelte Kategorie hinzugefügt wird
    $sql = "INSERT INTO ausruestungskategorien (name) VALUES (:name)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $newCategoryName);

    try {
        $stmt->execute();
        logAction('INSERT', 'ausruestungskategorien', 'name: ' . $newCategoryName);
        echo json_encode(['success' => true, 'message' => 'Kategorie erfolgreich hinzugefügt.']);
    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Fehler beim Hinzufügen der Kategorie.']);
    }
} else {
    header('Location: ../error.php');
    exit;
}

$conn = null;

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
