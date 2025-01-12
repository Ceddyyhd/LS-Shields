<?php
include 'security_check.php'; // Sicherheitsprüfung für diese Datei

// Fehleranzeige aktivieren
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Einbinden der Datenbankverbindung
include('db.php');

// Überprüfen, ob die Event-ID über POST übergeben wurde
if (isset($_POST['event_id'])) {
    $eventId = $_POST['event_id'];  // Event ID aus dem POST-Request holen
} else {
    die('Keine Eventplanungs-ID angegeben.');
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
        echo "Fehler bei der JSON-Codierung."; // Diese Nachricht wird an den Browser gesendet
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
            echo "Daten wurden erfolgreich gespeichert!";  // Diese Nachricht wird an den Browser gesendet
        } else {
            error_log("Fehler beim Ausführen des UPDATE-Statements: " . implode(", ", $stmt->errorInfo())); // Protokolliere SQL-Fehler
            echo "Fehler beim Speichern der Daten.";  // Diese Nachricht wird an den Browser gesendet
        }
    } catch (PDOException $e) {
        // Fehlerbehandlung: Transaktion zurücksetzen
        $conn->rollBack();
        error_log("Fehler: " . $e->getMessage());  // Protokolliere den Fehler
        echo "Fehler: " . $e->getMessage();  // Diese Nachricht wird an den Browser gesendet
    }
} else {
    echo "Fehlende Team-Daten!";  // Diese Nachricht wird an den Browser gesendet, wenn keine Daten gesendet wurden
}
?>
