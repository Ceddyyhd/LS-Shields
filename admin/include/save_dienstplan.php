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

                // Überprüfen der maximalen Zeit und gearbeitete Zeit
                $maxTime = !empty($_POST['max_time_' . $employeeId]) ? $_POST['max_time_' . $employeeId] : NULL;
                $workTime = !empty($_POST['work_time_' . $employeeId]) ? $_POST['work_time_' . $employeeId] : NULL;

                // Formatieren der Maximal da bis Zeit (nur Stunden und Minuten)
                if ($maxTime) {
                    $maxTime = date('H:i', strtotime($maxTime));  // Nur Stunden und Minuten
                }

                // Formatieren der Gearbeiteten Zeit (Start- und Endzeit)
                if ($workTime) {
                    $workTime = date('m/d/Y h:i A', strtotime($workTime));  // Format: MM/DD/YYYY hh:mm AM/PM
                }

                // Prüfen, ob bereits ein Dienstplan-Eintrag für diesen Mitarbeiter existiert
                $stmt = $conn->prepare("SELECT * FROM dienstplan WHERE event_id = :event_id AND employee_id = :employee_id");
                $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
                $stmt->bindParam(':employee_id', $employeeId, PDO::PARAM_INT);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    // Update, falls bereits ein Eintrag existiert
                    $stmt = $conn->prepare("
                        UPDATE dienstplan 
                        SET max_time = :max_time, work_time = :work_time
                        WHERE event_id = :event_id AND employee_id = :employee_id
                    ");
                } else {
                    // Insert, falls noch kein Eintrag existiert
                    $stmt = $conn->prepare("
                        INSERT INTO dienstplan (event_id, employee_id, max_time, work_time)
                        VALUES (:event_id, :employee_id, :max_time, :work_time)
                    ");
                }

                $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
                $stmt->bindParam(':employee_id', $employeeId, PDO::PARAM_INT);
                $stmt->bindParam(':max_time', $maxTime, PDO::PARAM_STR);
                $stmt->bindParam(':work_time', $workTime, PDO::PARAM_STR);
                $stmt->execute();
            }
        }

        // Erfolgsantwort zurückgeben
        echo json_encode(['status' => 'success', 'message' => 'Dienstplan erfolgreich gespeichert!']);
    } catch (PDOException $e) {
        // Fehlerbehandlung
        echo json_encode(['status' => 'error', 'message' => 'Fehler beim Speichern: ' . $e->getMessage()]);
    }
}
?>
