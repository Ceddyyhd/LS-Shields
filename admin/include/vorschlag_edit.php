<?php
include 'db.php'; // Datenbankverbindung
session_start(); // Sitzung starten

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Überprüfen, ob das CSRF-Token gültig ist
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false, 'message' => 'Ungültiges CSRF-Token']);
        exit;
    }

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

        // Log-Eintrag in die Datenbank einfügen
        $logQuery = "INSERT INTO vorschlag_logs (vorschlag_id, action, user_name) VALUES (:vorschlag_id, :action, :user_name)";
        $logStmt = $conn->prepare($logQuery);
        $logStmt->execute([
            ':vorschlag_id' => $id,
            ':action' => $logMessage,
            ':user_name' => $user_name
        ]);

        // Allgemeiner Log-Eintrag
        logAction('UPDATE', 'verbesserungsvorschlaege', 'Vorschlag bearbeitet: ID: ' . $id . ', bearbeitet von: ' . $_SESSION['user_id']);

        echo json_encode(['success' => true, 'message' => 'Vorschlag erfolgreich bearbeitet.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
    }
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
