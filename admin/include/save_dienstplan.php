<?php
// Verbindung zur Datenbank
include('db.php');
session_start();

// Event ID aus der URL holen
$eventId = $_GET['id'];

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Ungültiges CSRF-Token']);
    exit;
}

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
                $stmt->execute([':event_id' => $eventId, ':employee_id' => $employeeId]);
                $existingEntry = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($existingEntry) {
                    // Update der bestehenden Einträge
                    $stmt = $conn->prepare("
                        UPDATE dienstplan 
                        SET max_time = :max_time, gestartet_um = :gestartet_um, gegangen_um = :gegangen_um, arbeitszeit = :arbeitszeit 
                        WHERE event_id = :event_id AND employee_id = :employee_id
                    ");
                } else {
                    // Einfügen neuer Einträge
                    $stmt = $conn->prepare("
                        INSERT INTO dienstplan (event_id, employee_id, max_time, gestartet_um, gegangen_um, arbeitszeit) 
                        VALUES (:event_id, :employee_id, :max_time, :gestartet_um, :gegangen_um, :arbeitszeit)
                    ");
                }

                $stmt->execute([
                    ':event_id' => $eventId,
                    ':employee_id' => $employeeId,
                    ':max_time' => $maxTime,
                    ':gestartet_um' => $gestartetUm,
                    ':gegangen_um' => $gegangenUm,
                    ':arbeitszeit' => $arbeitszeit
                ]);

                // Log-Eintrag für die Änderungen
                logAction('UPDATE', 'dienstplan', 'event_id: ' . $eventId . ', employee_id: ' . $employeeId . ', changes: ' . json_encode($_POST) . ', edited_by: ' . $_SESSION['user_id']);
            }
        }

        echo json_encode(['success' => true, 'message' => 'Dienstplan erfolgreich aktualisiert.']);
    } catch (PDOException $e) {
        error_log('Fehler beim Aktualisieren des Dienstplans: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Fehler beim Aktualisieren des Dienstplans: ' . $e->getMessage()]);
    }
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
