<?php
require 'db.php';  // Deine DB-Verbindung

// Überprüfen, ob der Benutzer die Berechtigung zum Löschen von Dokumenten hat

    // Sicherstellen, dass eine Dokument-ID übergeben wurde
    if (isset($_POST['document_id'])) {
        $document_id = (int) $_POST['document_id'];
        // Debugging-Ausgabe der übergebenen document_id
        error_log('Received document_id: ' . $document_id);
    
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
    
?>
