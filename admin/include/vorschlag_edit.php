<?php
include 'db.php'; // Datenbankverbindung

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Empfangene Daten
        $id = $_POST['id'];
        $bereich = isset($_POST['bereich']) ? $_POST['bereich'] : '';
        $betreff = isset($_POST['betreff']) ? $_POST['betreff'] : '';
        $vorschlag = isset($_POST['vorschlag']) ? $_POST['vorschlag'] : '';
        $status = isset($_POST['status']) ? $_POST['status'] : '';
        $notiz = isset($_POST['notiz']) ? $_POST['notiz'] : '';
        $anonym = isset($_POST['fuel_checked']) ? 1 : 0;
        $anonym = isset($_POST['fuel_checked']) ? 1 : 0;
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

        // SQL zum Aktualisieren des Vorschlags
        $updateQuery = "UPDATE verbesserungsvorschlaege SET 
                        bereich = :bereich, 
                        betreff = :betreff, 
                        vorschlag = :vorschlag, 
                        status = :status, 
                        notiz = :notiz, 
                        anonym = :anonym 
                        WHERE id = :id";

        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bindParam(':bereich', $bereich);
        $updateStmt->bindParam(':betreff', $betreff);
        $updateStmt->bindParam(':vorschlag', $vorschlag);
        $updateStmt->bindParam(':status', $status);
        $updateStmt->bindParam(':notiz', $notiz);
        $updateStmt->bindParam(':anonym', $anonym);
        $updateStmt->bindParam(':id', $id);

        if (!$updateStmt->execute()) {
            echo json_encode(['success' => false, 'message' => 'Fehler beim Bearbeiten des Vorschlags']);
            exit;
        }

        // Log-Eintrag erstellen
        $logMessage = "Vorschlag ID $id geändert von $user_name.\n";
        $logMessage .= "Änderungen: \n";
        
        // Vergleiche alte und neue Daten, um die Änderungen zu protokollieren
        if ($bereich != $oldData['bereich']) $logMessage .= "Bereich geändert: {$oldData['bereich']} -> $bereich\n";
        if ($betreff != $oldData['betreff']) $logMessage .= "Betreff geändert: {$oldData['betreff']} -> $betreff\n";
        if ($vorschlag != $oldData['vorschlag']) $logMessage .= "Vorschlag geändert: {$oldData['vorschlag']} -> $vorschlag\n";
        if ($status != $oldData['status']) $logMessage .= "Status geändert: {$oldData['status']} -> $status\n";
        if ($notiz != $oldData['notiz']) $logMessage .= "Notiz geändert: {$oldData['notiz']} -> $notiz\n";
        if ($anonym != $oldData['anonym']) $logMessage .= "Anonym geändert: {$oldData['anonym']} -> $anonym\n";

        // SQL zum Hinzufügen eines Log-Eintrags
        $logQuery = "INSERT INTO vorschlag_logs (vorschlag_id, user_name, change_details) VALUES (:vorschlag_id, :user_name, :change_details)";
        $logStmt = $conn->prepare($logQuery);
        $logStmt->bindParam(':vorschlag_id', $id);
        $logStmt->bindParam(':user_name', $user_name);
        $logStmt->bindParam(':change_details', $logMessage);

        if ($logStmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Fehler beim Loggen']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
    }
}
?>
