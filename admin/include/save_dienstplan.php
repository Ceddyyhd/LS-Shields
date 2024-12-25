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
            if (strpos($key, 'start_time_') === 0) {
                // Extrahiere die Mitarbeiter-ID aus dem Feldnamen
                $employeeId = substr($key, 11);

                // Überprüfen der Start- und Endzeit
                $startTime = !empty($_POST['start_time_' . $employeeId]) ? $_POST['start_time_' . $employeeId] : NULL;
                $endTime = !empty($_POST['end_time_' . $employeeId]) ? $_POST['end_time_' . $employeeId] : NULL;

                // Formatieren der Zeiten auf Y-m-d H:i:s (für korrekte Speicherung in der DB)
                if ($startTime) {
                    $startTime = date('Y-m-d H:i:s', strtotime($startTime));  // Umwandeln auf das richtige Format
                }
                if ($endTime) {
                    $endTime = date('Y-m-d H:i:s', strtotime($endTime));  // Umwandeln auf das richtige Format
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
                        SET start_time = :start_time, end_time = :end_time
                        WHERE event_id = :event_id AND employee_id = :employee_id
                    ");
                } else {
                    // Insert, falls noch kein Eintrag existiert
                    $stmt = $conn->prepare("
                        INSERT INTO dienstplan (event_id, employee_id, start_time, end_time)
                        VALUES (:event_id, :employee_id, :start_time, :end_time)
                    ");
                }

                $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
                $stmt->bindParam(':employee_id', $employeeId, PDO::PARAM_INT);
                $stmt->bindParam(':start_time', $startTime, PDO::PARAM_STR);
                $stmt->bindParam(':end_time', $endTime, PDO::PARAM_STR);
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
