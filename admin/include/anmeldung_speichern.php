<?php
// Verbindung zur Datenbank
include('db.php');

// Überprüfen, ob Daten gesendet wurden
if (isset($_POST['event_id']) && isset($_POST['employees'])) {
    $eventId = $_POST['event_id'];
    $employeeIds = $_POST['employees'];

    try {
        // Alle ausgewählten Mitarbeiter für das Event in die event_mitarbeiter_anmeldung-Tabelle eintragen
        $stmt = $conn->prepare("INSERT INTO event_mitarbeiter_anmeldung (event_id, employee_id) VALUES (:event_id, :employee_id)");

        // Jeden Mitarbeiter eintragen
        foreach ($employeeIds as $employeeId) {
            $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
            $stmt->bindParam(':employee_id', $employeeId, PDO::PARAM_INT);
            $stmt->execute();
        }

        echo "Anmeldung erfolgreich!";
    } catch (PDOException $e) {
        echo 'Fehler beim Speichern der Anmeldung: ' . $e->getMessage();
    }
}
?>
