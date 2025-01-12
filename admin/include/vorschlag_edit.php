<?php
include 'security_check.php'; // Sicherheitsprüfung für diese Datei

include 'db.php'; // Datenbankverbindung

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Empfangene Daten aus dem Formular
        $id = $_POST['id'];
        $status = isset($_POST['status']) ? $_POST['status'] : '';
        $notiz = isset($_POST['notiz']) ? $_POST['notiz'] : '';
        $user_name = $_POST['user_name']; // Benutzername aus dem versteckten Input
        
        // Abrufen der aktuellen Vorschlagsdaten für das Log
        $query = "SELECT * FROM verbesserungsvorschlaege WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $oldData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$oldData) {
            echo json_encode(['success' => false, 'message' => 'Vorschlag nicht gefunden']);
            exit;
        }

        // SQL zum Aktualisieren des Vorschlags, nur für Status und Notiz
        $updateQuery = "UPDATE verbesserungsvorschlaege SET 
                        status = :status, 
                        notiz = :notiz 
                        WHERE id = :id";

        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bindParam(':status', $status);
        $updateStmt->bindParam(':notiz', $notiz);
        $updateStmt->bindParam(':id', $id);

        // Update durchführen
        if (!$updateStmt->execute()) {
            echo json_encode(['success' => false, 'message' => 'Fehler beim Bearbeiten des Vorschlags']);
            exit;
        }

        // Log-Eintrag erstellen
        $logMessage = "Vorschlag ID $id geändert von $user_name.\n";
        $logMessage .= "Änderungen: \n";
        
        // Vergleiche alte und neue Daten, um die Änderungen zu protokollieren
        if ($status != $oldData['status']) $logMessage .= "Status geändert: {$oldData['status']} -> $status\n";
        if ($notiz != $oldData['notiz']) $logMessage .= "Notiz geändert: {$oldData['notiz']} -> $notiz\n";

        // SQL zum Hinzufügen eines Log-Eintrags
        $logQuery = "INSERT INTO vorschlag_logs (vorschlag_id, user_name, change_details) VALUES (:vorschlag_id, :user_name, :change_details)";
        $logStmt = $conn->prepare($logQuery);
        $logStmt->bindParam(':vorschlag_id', $id);
        $logStmt->bindParam(':user_name', $user_name);
        $logStmt->bindParam(':change_details', $logMessage);

        // Log-Eintrag ausführen
        if ($logStmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Fehler beim Loggen']);
        }
    } catch (Exception $e) {
        // Fehlerbehandlung
        echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
    }
}
?>
