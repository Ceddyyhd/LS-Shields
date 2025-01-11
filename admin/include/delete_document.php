<?php
require 'db.php';  // Deine DB-Verbindung
session_start();
header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'error' => 'Ungültiges CSRF-Token']);
    exit;
}

// Sicherstellen, dass eine Dokument-ID übergeben wurde
if (isset($_POST['document_id'])) {
    $document_id = (int) $_POST['document_id'];
    // Debugging-Ausgabe der übergebenen document_id
    error_log('Received document_id: ' . $document_id);

    try {
        // SQL-Abfrage zum Überprüfen, ob das Dokument existiert
        $sql = "SELECT file_path FROM documents WHERE id = :document_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':document_id', $document_id, PDO::PARAM_INT);
        $stmt->execute();
        $doc = $stmt->fetch(PDO::FETCH_ASSOC);

        // Überprüfen, ob das Dokument gefunden wurde
        if ($doc) {
            // Das Dokument löschen
            $deleteSql = "DELETE FROM documents WHERE id = :document_id";
            $deleteStmt = $conn->prepare($deleteSql);
            $deleteStmt->bindParam(':document_id', $document_id, PDO::PARAM_INT);
            if ($deleteStmt->execute()) {
                // Datei auf dem Server löschen
                if (file_exists($doc['file_path'])) {
                    unlink($doc['file_path']);
                }

                // Log-Eintrag für das Löschen
                logAction('DELETE', 'documents', 'document_id: ' . $document_id . ', deleted_by: ' . $_SESSION['user_id']);

                echo json_encode(['success' => true, 'message' => 'Dokument erfolgreich gelöscht']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Fehler beim Löschen des Dokuments']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Dokument nicht gefunden']);
        }
    } catch (PDOException $e) {
        error_log('Fehler beim Löschen des Dokuments: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Fehler beim Löschen des Dokuments: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Keine Dokument-ID übergeben']);
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
