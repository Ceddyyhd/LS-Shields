<?php
// Fehleranzeige aktivieren
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Überprüfen, ob das Formular abgeschickt wurde
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Event ID und alle übermittelten Daten aus dem Formular
    $eventId = $_GET['id'];

    try {
        // Verbindung zur Datenbank
        include('db.php');

        // Für jeden Mitarbeiter die übermittelten Daten speichern
        foreach ($_POST as $key => $value) {
            // Nur die Felder bearbeiten, die mit einem Mitarbeiter zusammenhängen
            if (strpos($key, 'max_time_') === 0) {
                // Extrahiere die Mitarbeiter-ID aus dem Feldnamen
                $employeeId = substr($key, 9);

                // Überprüfe, ob die gearbeitete Zeit leer ist, und setze sie auf NULL oder '00:00:00', wenn ja
                $workTime = !empty($_POST['work_time_' . $employeeId]) ? $_POST['work_time_' . $employeeId] : NULL;

                // Hier kannst du die Daten in der Tabelle für den Dienstplan speichern
                $stmt = $conn->prepare("
                    INSERT INTO dienstplan (event_id, employee_id, max_time, work_time)
                    VALUES (:event_id, :employee_id, :max_time, :work_time)
                ");
                $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
                $stmt->bindParam(':employee_id', $employeeId, PDO::PARAM_INT);
                $stmt->bindParam(':max_time', $_POST['max_time_' . $employeeId], PDO::PARAM_STR);
                $stmt->bindParam(':work_time', $workTime, PDO::PARAM_STR);
                $stmt->execute();
            }
        }

        // Bestätigung nach erfolgreichem Speichern
        echo 'Dienstplan erfolgreich gespeichert!';
    } catch (PDOException $e) {
        // Fehlerbehandlung, falls die Speicherung fehlschlägt
        echo 'Fehler beim Speichern: ' . $e->getMessage();
    }
}
?>
