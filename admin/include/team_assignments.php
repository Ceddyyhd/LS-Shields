<?php
// Fehleranzeige aktivieren
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Einbinden der Datenbankverbindung
include('db.php');
session_start();
header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Ungültiges CSRF-Token']);
    exit;
}

// Überprüfen, ob die Event-ID über POST übergeben wurde
if (isset($_POST['event_id'])) {
    $eventId = $_POST['event_id'];  // Event ID aus dem POST-Request holen
} else {
    echo json_encode(['success' => false, 'message' => 'Keine Eventplanungs-ID angegeben.']);
    exit;
}

// Überprüfen, ob die Team-Daten gesendet wurden
if (isset($_POST['teams']) && !empty($_POST['teams'])) {
    $teamData = $_POST['teams'];

    // Fehlerprotokollierung: Ausgabe der empfangenen Team-Daten
    error_log("Empfangene Team-Daten: " . print_r($teamData, true));  // Diese Zeile gibt die empfangenen Daten im Log aus

    // Die Teamdaten in JSON umwandeln
    $teamDataJson = json_encode($teamData);

    // Überprüfen, ob JSON korrekt codiert wurde
    if ($teamDataJson === false) {
        error_log("Fehler bei der JSON-Codierung: " . json_last_error_msg());  // Fehler bei der JSON-Codierung
        echo json_encode(['success' => false, 'message' => 'Fehler bei der JSON-Codierung.']);
        exit;
    }

    try {
        // Beginne die Transaktion
        $conn->beginTransaction();

        // UPDATE-Statement für das bestehende Event mit der entsprechenden ID
        $stmt = $conn->prepare("UPDATE eventplanung SET team_verteilung = :team_verteilung WHERE id = :id");
        $stmt->bindParam(':team_verteilung', $teamDataJson, PDO::PARAM_STR);
        $stmt->bindParam(':id', $eventId, PDO::PARAM_INT);

        // Führe das UPDATE-Statement aus
        if ($stmt->execute()) {
            // Bestätigen der Transaktion
            $conn->commit();

            // Log-Eintrag für die Änderungen
            logAction('UPDATE', 'eventplanung', 'event_id: ' . $eventId . ', team_verteilung aktualisiert von: ' . $_SESSION['user_id']);

            echo json_encode(['success' => true, 'message' => 'Daten wurden erfolgreich gespeichert!']);
        } else {
            error_log("Fehler beim Ausführen des UPDATE-Statements: " . implode(", ", $stmt->errorInfo())); // Protokolliere SQL-Fehler
            echo json_encode(['success' => false, 'message' => 'Fehler beim Ausführen des UPDATE-Statements.']);
        }
    } catch (PDOException $e) {
        // Rollback der Transaktion bei Fehler
        $conn->rollBack();
        error_log('Fehler beim Speichern der Daten: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern der Daten: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Keine Team-Daten gesendet.']);
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
