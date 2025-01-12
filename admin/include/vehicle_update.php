<?php
include 'security_check.php'; // Sicherheitsprüfung für diese Datei

include 'db.php';  // Datenbankverbindung einbinden
session_start();   // Session starten, um auf $_SESSION zuzugreifen

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Werte aus dem POST holen
    $vehicle_id = $_POST['vehicle_id'];
    $model = $_POST['model'];
    $license_plate = $_POST['license_plate'];
    $location = $_POST['location'];
    $next_inspection = $_POST['next_inspection'];
    $user_name = $_SESSION['username'];  // Benutzername aus der Session holen

    // Zusätzliche Felder (für Logs und die vehicles DB)
    $notes = isset($_POST['notes']) ? $_POST['notes'] : NULL;  // Notizen
    $decommissioned = isset($_POST['decommissioned']) ? 1 : 0;  // Checkbox "Ausgemustert"

    // Tuning-Optionen
    $turbo_tuning = isset($_POST['turbo_tuning']) ? 1 : 0;
    $engine_tuning = isset($_POST['engine_tuning']) ? 1 : 0;
    $transmission_tuning = isset($_POST['transmission_tuning']) ? 1 : 0;
    $brake_tuning = isset($_POST['brake_tuning']) ? 1 : 0;

    try {
        // Vorherige Fahrzeugdaten abrufen
        $sql_select = "SELECT * FROM vehicles WHERE id = ?";
        $stmt_select = $conn->prepare($sql_select);
        $stmt_select->execute([$vehicle_id]);
        $old_vehicle = $stmt_select->fetch(PDO::FETCH_ASSOC);

        // Überprüfen, ob das Fahrzeug existiert
        if (!$old_vehicle) {
            echo json_encode(['success' => false, 'message' => 'Fahrzeug nicht gefunden.']);
            exit;
        }

        // Fahrzeugdaten in der DB aktualisieren (jetzt auch Notizen und Ausgemustert)
        $sql_update = "UPDATE vehicles SET 
            model = ?, license_plate = ?, location = ?, next_inspection = ?, 
            notes = ?, decommissioned = ?, turbo_tuning = ?, engine_tuning = ?, 
            transmission_tuning = ?, brake_tuning = ? 
            WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->execute([
            $model, $license_plate, $location, $next_inspection, 
            $notes, $decommissioned, $turbo_tuning, $engine_tuning, 
            $transmission_tuning, $brake_tuning, $vehicle_id
        ]);

        // Änderungen ermitteln und formatieren
        $changes = [];
        if ($model !== $old_vehicle['model']) {
            $changes[] = "Modell: " . $old_vehicle['model'] . " -> " . $model;
        }
        if ($license_plate !== $old_vehicle['license_plate']) {
            $changes[] = "Kennzeichen: " . $old_vehicle['license_plate'] . " -> " . $license_plate;
        }
        if ($location !== $old_vehicle['location']) {
            $changes[] = "Standort: " . $old_vehicle['location'] . " -> " . $location;
        }
        if ($next_inspection !== $old_vehicle['next_inspection']) {
            $changes[] = "Nächste Inspektion: " . $old_vehicle['next_inspection'] . " -> " . $next_inspection;
        }
        if ($notes !== $old_vehicle['notes']) {
            $changes[] = "Notizen: " . $old_vehicle['notes'] . " -> " . $notes;
        }
        if ($decommissioned != $old_vehicle['decommissioned']) {
            $changes[] = "Ausgemustert: " . ($decommissioned ? 'Ja' : 'Nein');
        }
        if ($turbo_tuning != $old_vehicle['turbo_tuning']) {
            $changes[] = "Turbotuning: " . ($turbo_tuning ? 'Ja' : 'Nein');
        }
        if ($engine_tuning != $old_vehicle['engine_tuning']) {
            $changes[] = "Motortuning: " . ($engine_tuning ? 'Ja' : 'Nein');
        }
        if ($transmission_tuning != $old_vehicle['transmission_tuning']) {
            $changes[] = "Getriebetuning: " . ($transmission_tuning ? 'Ja' : 'Nein');
        }
        if ($brake_tuning != $old_vehicle['brake_tuning']) {
            $changes[] = "Bremsentuning: " . ($brake_tuning ? 'Ja' : 'Nein');
        }

        // Wenn keine Änderungen vorgenommen wurden
        if (empty($changes)) {
            echo json_encode(['success' => false, 'message' => 'Keine Änderungen vorgenommen.']);
            exit;
        }

        // Log-Eintrag für die Änderungen erstellen
        $action = "Fahrzeug bearbeitet ($license_plate) (" . implode(", ", $changes) . ")";  // Änderungen in der gewünschten Formatierung zusammenfügen

        // Log-Eintrag in die vehicle_logs-Tabelle einfügen
        $log_sql = "INSERT INTO vehicles_logs (vehicle_id, action, user_name) VALUES (?, ?, ?)";
        $log_stmt = $conn->prepare($log_sql);
        $log_stmt->execute([$vehicle_id, $action, $user_name]);  // Benutzername zum Log hinzufügen

        // Erfolgsantwort zurückgeben
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        // Fehlerprotokollierung für PDO-Fehler
        error_log("PDO Fehler beim Bearbeiten des Fahrzeugs: " . $e->getMessage());

        echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
    } catch (Exception $e) {
        // Allgemeine Fehlerbehandlung
        echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
    }
}
?>
