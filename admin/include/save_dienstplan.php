<?php
// Verbindung zur Datenbank
include('db.php');

// Event ID aus der URL holen
$eventId = $_GET['id'];

// Überprüfen, ob die erforderlichen Daten über POST übermittelt wurden
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Alle Mitarbeiter aus dem Formular durchlaufen
        foreach ($_POST as $key => $value) {
            // Nur die relevanten Felder bearbeiten (max_time, gestartet_um, gegangen_um)
            if (strpos($key, 'max_time_') !== false) {
                $employeeId = str_replace('max_time_', '', $key); // Mitarbeiter-ID extrahieren
                $maxTime = $value;
                $gestartetUm = isset($_POST['gestartet_um_' . $employeeId]) ? $_POST['gestartet_um_' . $employeeId] : null;
                $gegangenUm = isset($_POST['gegangen_um_' . $employeeId]) ? $_POST['gegangen_um_' . $employeeId] : null;

                // Überprüfen, ob der Wert leer ist und als NULL setzen
                if ($gestartetUm === '') {
                    $gestartetUm = null;
                }
                if ($gegangenUm === '') {
                    $gegangenUm = null;
                }

                // Wenn max_time leer ist, NULL setzen
                if ($maxTime === '') {
                    $maxTime = null;
                }

                // SQL-Abfrage zum Aktualisieren oder Einfügen der Daten
                $stmt = $conn->prepare("
                    SELECT id FROM dienstplan 
                    WHERE event_id = :event_id AND employee_id = :employee_id
                ");
                $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
                $stmt->bindValue(':employee_id', $employeeId, PDO::PARAM_INT);
                $stmt->execute();
                $existingEntry = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($existingEntry) {
                    // Datensatz existiert, also ein UPDATE ausführen
                    $stmt = $conn->prepare("
                        UPDATE dienstplan
                        SET max_time = :max_time, gestartet_um = :gestartet_um, gegangen_um = :gegangen_um
                        WHERE id = :id
                    ");
                    // Parameter binden
                    $stmt->bindValue(':id', $existingEntry['id'], PDO::PARAM_INT);
                } else {
                    // Datensatz existiert nicht, also ein INSERT ausführen
                    $stmt = $conn->prepare("
                        INSERT INTO dienstplan (event_id, employee_id, max_time, gestartet_um, gegangen_um)
                        VALUES (:event_id, :employee_id, :max_time, :gestartet_um, :gegangen_um)
                    ");
                }

                // Parameter binden und sicherstellen, dass NULL für leere Felder übergeben wird
                $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
                $stmt->bindValue(':employee_id', $employeeId, PDO::PARAM_INT);

                // Wenn max_time null ist, sicherstellen, dass NULL übergeben wird
                if ($maxTime === null) {
                    $stmt->bindValue(':max_time', null, PDO::PARAM_NULL);  // NULL für max_time
                } else {
                    $stmt->bindValue(':max_time', $maxTime, PDO::PARAM_STR);  // Wenn nicht NULL, dann String
                }

                // Wenn gestartet_um oder gegangen_um null ist, sicherstellen, dass NULL übergeben wird
                if ($gestartetUm === null) {
                    $stmt->bindValue(':gestartet_um', null, PDO::PARAM_NULL);  // NULL für gestartet_um
                } else {
                    $stmt->bindValue(':gestartet_um', $gestartetUm, PDO::PARAM_STR);  // Wenn nicht NULL, dann String
                }

                if ($gegangenUm === null) {
                    $stmt->bindValue(':gegangen_um', null, PDO::PARAM_NULL);  // NULL für gegangen_um
                } else {
                    $stmt->bindValue(':gegangen_um', $gegangenUm, PDO::PARAM_STR);  // Wenn nicht NULL, dann String
                }

                // Execute the query
                $stmt->execute();
            }
        }

        // Erfolgsantwort zurückgeben
        echo json_encode(['status' => 'success', 'message' => 'Daten wurden erfolgreich gespeichert!']);
    } catch (PDOException $e) {
        // Fehler im SQL-Code
        echo json_encode(['status' => 'error', 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
    } catch (Exception $e) {
        // Allgemeiner Fehler
        echo json_encode(['status' => 'error', 'message' => 'Fehler: ' . $e->getMessage()]);
    }
}
?>
