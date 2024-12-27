<?php
// Fehleranzeige aktivieren
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Überprüfen, ob die Daten gesendet wurden
if (isset($_POST['event_id']) && isset($_POST['employees']) && isset($_POST['notizen'])) {
    $eventId = $_POST['event_id'];
    $employeeIds = $_POST['employees'];
    $notizen = $_POST['notizen'];  // Notizen von der Anfrage holen

    // Überprüfen, ob $employeeIds ein Array ist
    if (!is_array($employeeIds)) {
        // Falls es kein Array ist, in ein Array umwandeln
        $employeeIds = explode(',', $employeeIds);
    }

    // Ausgabe der übermittelten Daten zur Fehlerbehebung
    var_dump($eventId, $employeeIds, $notizen);

    try {
        // Verbindung zur Datenbank
        include('db.php');

        // Alle ausgewählten Mitarbeiter für das Event in die event_mitarbeiter_anmeldung-Tabelle eintragen
        $stmt = $conn->prepare("INSERT INTO event_mitarbeiter_anmeldung (event_id, employee_id, notizen) VALUES (:event_id, :employee_id, :notizen)");

        foreach ($employeeIds as $employeeId) {
            $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
            $stmt->bindParam(':employee_id', $employeeId, PDO::PARAM_INT);
            $stmt->bindParam(':notizen', $notizen, PDO::PARAM_STR); // Notizen binden
            $stmt->execute();
        }

        echo "Anmeldung erfolgreich!";
    } catch (PDOException $e) {
        echo 'Fehler beim Speichern der Anmeldung: ' . $e->getMessage();
    }
} else {
    echo 'Fehlende Daten!';
}

?>
