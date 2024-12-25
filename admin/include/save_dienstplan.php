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

                // Überprüfen der maximalen Zeit und die neuen Felder
                $maxTime = !empty($_POST['max_time']) ? $_POST['max_time'] : NULL;
                $gestartetUm = !empty($_POST['gestartet_um']) ? $_POST['gestartet_um'] : NULL;
                $gegangenUm = !empty($_POST['gegangen_um']) ? $_POST['gegangen_um'] : NULL;

                // Wenn das Maximal da bis-Feld leer ist, auf NULL setzen
                if ($maxTime) {
                    $maxTime = date('H:i', strtotime($maxTime));  // Nur Stunden und Minuten
                } else {
                    $maxTime = NULL; // Setze auf NULL, wenn leer
                }

                // Wenn das "Gestartet Um"-Feld ausgefüllt ist, setze den Wert
                if ($gestartetUm) {
                    $parsedGestartetUm = strtotime($gestartetUm);
                    if ($parsedGestartetUm !== false) {
                        $gestartetUm = date('Y-m-d H:i:s', $parsedGestartetUm);  // Format: Y-m-d H:i:s für die DB
                    } else {
                        $gestartetUm = NULL; // Wenn ungültig, setze auf NULL
                    }
                }

                // Wenn das "Gegangen Um"-Feld ausgefüllt ist, setze den Wert
                if ($gegangenUm) {
                    $parsedGegangenUm = strtotime($gegangenUm);
                    if ($parsedGegangenUm !== false) {
                        $gegangenUm = date('Y-m-d H:i:s', $parsedGegangenUm);  // Format: Y-m-d H:i:s für die DB
                    } else {
                        $gegangenUm = NULL; // Wenn ungültig, setze auf NULL
                    }
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
                        SET max_time = :max_time, gestartet_um = :gestartet_um, gegangen_um = :gegangen_um
                        WHERE event_id = :event_id AND employee_id = :employee_id
                    ");
                } else {
                    // Insert, falls noch kein Eintrag existiert
                    $stmt = $conn->prepare("
                        INSERT INTO dienstplan (event_id, employee_id, max_time, gestartet_um, gegangen_um)
                        VALUES (:event_id, :employee_id, :max_time, :gestartet_um, :gegangen_um)
                    ");
                }

                $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
                $stmt->bindParam(':employee_id', $employeeId, PDO::PARAM_INT);
                $stmt->bindParam(':max_time', $maxTime, PDO::PARAM_STR);
                $stmt->bindParam(':gestartet_um', $gestartetUm, PDO::PARAM_STR);
                $stmt->bindParam(':gegangen_um', $gegangenUm, PDO::PARAM_STR);
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
