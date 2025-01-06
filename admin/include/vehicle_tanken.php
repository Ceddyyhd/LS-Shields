<?php
include 'db.php';  // Datenbankverbindung einbinden
session_start();   // Session starten, um auf $_SESSION zuzugreifen

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Werte aus dem POST holen
    $vehicle_id = $_POST['vehicle_id'];
    $license_plate = $_POST['license_plate'];
    $fuel_checked = isset($_POST['fuel_checked']) ? 1 : 0;  // Checkbox "Getankt"
    $fuel_location = isset($_POST['fuel_location']) ? $_POST['fuel_location'] : NULL;  // Textfeld "Wo?"
    $fuel_amount = isset($_POST['fuel_amount']) ? $_POST['fuel_amount'] : NULL;  // Betrag
    $user_name = $_SESSION['username'];  // Benutzername aus der Session holen

    try {
        // Vorherige Fahrzeugdaten abrufen
        $sql_select = "SELECT * FROM vehicles WHERE id = ?";
        $stmt_select = $conn->prepare($sql_select);
        $stmt_select->execute([$vehicle_id]);
        $old_vehicle = $stmt_select->fetch(PDO::FETCH_ASSOC);

        if (!$old_vehicle) {
            echo json_encode(['success' => false, 'message' => 'Fahrzeug nicht gefunden.']);
            exit;
        }

        // Fahrzeugdaten in der DB aktualisieren
        $sql_update = "UPDATE vehicles SET fuel_checked = ?, fuel_location = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->execute([$fuel_checked, $fuel_location, $vehicle_id]);

        // Log-Eintrag für Tanken-Änderung
        $changes = [];
        if ($fuel_checked != $old_vehicle['fuel_checked']) {
            $changes[] = "Getankt: " . ($fuel_checked ? 'Ja' : 'Nein');
        }
        if ($fuel_location !== $old_vehicle['fuel_location']) {
            $changes[] = "Wo getankt: " . $old_vehicle['fuel_location'] . " -> " . $fuel_location;
        }

        if (!empty($changes)) {
            $action = "Fahrzeug getankt ($license_plate) (" . implode(", ", $changes) . ")";
            $log_sql = "INSERT INTO vehicles_logs (vehicle_id, action, user_name) VALUES (?, ?, ?)";
            $log_stmt = $conn->prepare($log_sql);
            $log_stmt->execute([$vehicle_id, $action, $user_name]);
        }

        // Wenn der Haken gesetzt ist und der Betrag angegeben ist (Deckel bezahlt)
        if ($fuel_checked) {
            // Eintrag in eine separate Tabelle für Ausgaben/Deckel
            $sql_finance = "INSERT INTO finances (typ, kategorie, notiz, betrag, erstellt_von) 
                            VALUES ('Ausgabe', 'Tanken', ?, ?, ?)";
            $stmt_finance = $conn->prepare($sql_finance);
            $stmt_finance->execute([
                "Getankt Kennzeichen: $license_plate in $fuel_location",
                $fuel_amount,
                $user_name
            ]);

            // Log für Finanztransaktion - Deckel
            $finance_log_sql = "INSERT INTO finances_logs (typ, kategorie, notiz, betrag, erstellt_von, vehicle_id) 
                                VALUES ('Ausgabe', 'Tanken', ?, ?, ?, ?)";
            $finance_log_stmt = $conn->prepare($finance_log_sql);
            $finance_log_stmt->execute([
                "Getankt Kennzeichen: $license_plate in $fuel_location",
                $fuel_amount,
                $user_name,
                $vehicle_id
            ]);
        } else {
            // Eintrag in die Finanz-Tabelle (wenn direkt vom Firmenkonto abgegangen)
            $sql_finance = "INSERT INTO finances (typ, kategorie, notiz, betrag, erstellt_von) 
                            VALUES ('Ausgabe', 'Tanken', ?, ?, ?)";
            $stmt_finance = $conn->prepare($sql_finance);
            $stmt_finance->execute([
                "Getankt Kennzeichen: $license_plate in $fuel_location",
                $fuel_amount,
                $user_name
            ]);

            // Log für Finanztransaktion - Firmenkonto
            $finance_log_sql = "INSERT INTO finances_logs (typ, kategorie, notiz, betrag, erstellt_von, vehicle_id) 
                                VALUES ('Ausgabe', 'Tanken', ?, ?, ?, ?)";
            $finance_log_stmt = $conn->prepare($finance_log_sql);
            $finance_log_stmt->execute([
                "Getankt Kennzeichen: $license_plate in $fuel_location",
                $fuel_amount,
                $user_name,
                $vehicle_id
            ]);
        }

        // Erfolgreiche Antwort zurückgeben
        echo json_encode(['success' => true, 'message' => 'Fahrzeugdaten und Tanken-Daten erfolgreich aktualisiert.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Aktualisieren: ' . $e->getMessage()]);
    }
}
?>
