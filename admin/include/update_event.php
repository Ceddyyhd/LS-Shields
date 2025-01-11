<?php
require 'db.php'; // Deine DB-Verbindung
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Überprüfen, ob das CSRF-Token gültig ist
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false, 'message' => 'Ungültiges CSRF-Token']);
        exit;
    }

    // Alle Formulardaten aus POST erhalten
    $event_id = $_POST['event_id']; // Event ID aus POST-Daten
    $vorname_nachname = $_POST['vorname_nachname'];
    $telefonnummer = $_POST['telefonnummer'];
    $datum_uhrzeit_event = $_POST['datum_uhrzeit_event']; // Datum und Uhrzeit direkt aus POST
    $ort = $_POST['ort'];
    $event_lead = $_POST['event_lead'];
    $event = $_POST['event']; // Neues Event-Feld
    $anmerkung = $_POST['anmerkung']; // Neues Anmerkung-Feld
    $status = $_POST['status']; // Der neue Status aus dem Formular

    // SQL-Update-Abfrage zum Aktualisieren der Event-Daten inklusive Status
    $sql = "UPDATE eventplanung SET
            vorname_nachname = :vorname_nachname,
            telefonnummer = :telefonnummer,
            datum_uhrzeit_event = :datum_uhrzeit_event,
            ort = :ort,
            event_lead = :event_lead,
            event = :event, 
            anmerkung = :anmerkung,
            status = :status  -- Hier wird der Status ebenfalls aktualisiert
            WHERE id = :event_id"; // 'id' statt 'event_id' in der WHERE-Klausel

    // Datenbankvorbereitung und Bindung der Parameter
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':vorname_nachname', $vorname_nachname);
    $stmt->bindParam(':telefonnummer', $telefonnummer);
    $stmt->bindParam(':datum_uhrzeit_event', $datum_uhrzeit_event);
    $stmt->bindParam(':ort', $ort);
    $stmt->bindParam(':event_lead', $event_lead);
    $stmt->bindParam(':event', $event);
    $stmt->bindParam(':anmerkung', $anmerkung);
    $stmt->bindParam(':status', $status); // Bindung des Status
    $stmt->bindParam(':event_id', $event_id);

    // Daten speichern und Antwort zurückgeben
    if ($stmt->execute()) {
        // Log-Eintrag für die Änderungen
        logAction('UPDATE', 'eventplanung', 'event_id: ' . $event_id . ', bearbeitet von: ' . $_SESSION['user_id']);
        
        echo json_encode(['message' => 'Änderungen erfolgreich gespeichert', 'status' => $status]);
    } else {
        // Fehlerausgabe, falls das Update fehlschlägt
        echo json_encode(['message' => 'Fehler beim Speichern der Änderungen', 'error' => $stmt->errorInfo()]);
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
