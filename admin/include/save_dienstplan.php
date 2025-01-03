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

                // Wenn max_time leer ist, NULL setzen
                if ($maxTime === '') {
                    $maxTime = null;
                }

                // Überprüfen, ob gestartet_um und gegangen_um leer sind und NULL zuweisen
                if ($gestartetUm === '') {
                    $gestartetUm = null;
                }
                if ($gegangenUm === '') {
                    $gegangenUm = null;
                }

                // Arbeitszeit berechnen, wenn sowohl gestartet_um als auch gegangen_um vorhanden sind
                $arbeitszeit = null;
                if ($gestartetUm !== null && $gegangenUm !== null) {
                    $startTime = new DateTime($gestartetUm);
                    $endTime = new DateTime($gegangenUm);
                    $interval = $startTime->diff($endTime);
                    
                    // Die Differenz in Stunden und Minuten berechnen
                    $arbeitszeit = $interval->days * 24 + $interval->h + $interval->i / 60;
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
                        SET max_time = :max_time, gestartet_um = :gestartet_um, gegangen_um = :gegangen_um, arbeitszeit = :arbeitszeit
                        WHERE event_id = :event_id AND employee_id = :employee_id
                    ");
                } else {
                    // Datensatz existiert nicht, also ein INSERT ausführen
                    $stmt = $conn->prepare("
                        INSERT INTO dienstplan (event_id, employee_id, max_time, gestartet_um, gegangen_um, arbeitszeit)
                        VALUES (:event_id, :employee_id, :max_time, :gestartet_um, :gegangen_um, :arbeitszeit)
                    ");
                }

                // Alle Parameter binden
                $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
                $stmt->bindValue(':employee_id', $employeeId, PDO::PARAM_INT);

                // Binde max_time, gestartet_um, gegangen_um nur, wenn sie nicht NULL sind
                if ($maxTime === null) {
                    $stmt->bindValue(':max_time', null, PDO::PARAM_NULL);
                } else {
                    $stmt->bindValue(':max_time', $maxTime, PDO::PARAM_STR);
                }

                if ($gestartetUm === null) {
                    $stmt->bindValue(':gestartet_um', null, PDO::PARAM_NULL);
                } else {
                    $stmt->bindValue(':gestartet_um', $gestartetUm, PDO::PARAM_STR);
                }

                if ($gegangenUm === null) {
                    $stmt->bindValue(':gegangen_um', null, PDO::PARAM_NULL);
                } else {
                    $stmt->bindValue(':gegangen_um', $gegangenUm, PDO::PARAM_STR);
                }

                // Binde die berechnete Arbeitszeit, wenn sie vorhanden ist
                if ($arbeitszeit !== null) {
                    $stmt->bindValue(':arbeitszeit', $arbeitszeit, PDO::PARAM_STR);
                } else {
                    $stmt->bindValue(':arbeitszeit', null, PDO::PARAM_NULL);
                }

                // SQL-Abfrage ausführen
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
