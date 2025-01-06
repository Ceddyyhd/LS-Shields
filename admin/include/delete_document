<?php
require 'db.php';  // Deine DB-Verbindung

// Überprüfen, ob der Benutzer die Berechtigung zum Löschen von Dokumenten hat
if (isset($_SESSION['permissions']['delete_documents']) && $_SESSION['permissions']['delete_documents']) {

    // Sicherstellen, dass eine Dokument-ID übergeben wurde
    if (isset($_POST['document_id'])) {
        $document_id = (int) $_POST['document_id'];

        // SQL-Abfrage, um das Dokument aus der Datenbank zu löschen
        $sql = "SELECT file_path FROM documents WHERE id = :document_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':document_id', $document_id, PDO::PARAM_INT);
        $stmt->execute();
        $doc = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($doc) {
            // Das Dokument aus der Datenbank löschen
            $deleteSql = "DELETE FROM documents WHERE id = :document_id";
            $deleteStmt = $conn->prepare($deleteSql);
            $deleteStmt->bindParam(':document_id', $document_id, PDO::PARAM_INT);
            if ($deleteStmt->execute()) {
                // Auch die Datei vom Server löschen
                if (file_exists($doc['file_path'])) {
                    unlink($doc['file_path']);
                }
                echo json_encode(['success' => true, 'message' => 'Dokument erfolgreich gelöscht']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Fehler beim Löschen des Dokuments']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Dokument nicht gefunden']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Keine Dokument-ID übergeben']);
    }
} else {
    // Falls der Benutzer keine Berechtigung hat, die Aktion auszuführen
    echo json_encode(['success' => false, 'message' => 'Keine Berechtigung für diese Aktion']);
}
?>
