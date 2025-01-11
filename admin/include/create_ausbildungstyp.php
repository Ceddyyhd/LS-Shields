<?php
require_once 'db.php'; // Deine DB-Verbindungsdatei
session_start();
header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header('Location: ../error.php');
    exit;
}

// Sicherstellen, dass alle erforderlichen Felder vorhanden sind
if (isset($_POST['key_name']) && isset($_POST['display_name']) && isset($_POST['description'])) {
    $key_name = filter_input(INPUT_POST, 'key_name', FILTER_SANITIZE_STRING);
    $display_name = filter_input(INPUT_POST, 'display_name', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);

    try {
        // SQL-Abfrage zum Einfügen eines neuen Ausbildungstyps
        $sql = "INSERT INTO ausbildungstypen (key_name, display_name, description) VALUES (:key_name, :display_name, :description)";
        $stmt = $conn->prepare($sql);
        
        // Binden der Parameter
        $stmt->bindParam(':key_name', $key_name);
        $stmt->bindParam(':display_name', $display_name);
        $stmt->bindParam(':description', $description);

        // Ausführen der Anfrage
        if ($stmt->execute()) {
            // Log-Eintrag für das Erstellen
            $ausbildungstyp_id = $conn->lastInsertId();
            logAction('INSERT', 'ausbildungstypen', 'ausbildungstyp_id: ' . $ausbildungstyp_id . ', created_by: ' . $_SESSION['user_id']);

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Fehler beim Erstellen des Ausbildungstyps.']);
        }
    } catch (PDOException $e) {
        error_log('Datenbankfehler: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Bitte alle Felder ausfüllen.']);
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
