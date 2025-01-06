<?php
include 'db.php';  // Datenbankverbindung einbinden
session_start();   // Session starten, um auf $_SESSION zuzugreifen

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Werte aus dem POST holen
    $vehicle_id = $_POST['vehicle_id'];
    $license_plate = $_POST['license_plate'];  // Kennzeichen
    $fuel_checked = isset($_POST['fuel_checked']) ? 1 : 0;  // Checkbox "Getankt"
    $fuel_location = isset($_POST['fuel_location']) ? $_POST['fuel_location'] : NULL;  // Textfeld "Wo?"
    $fuel_amount = isset($_POST['fuel_amount']) ? $_POST['fuel_amount'] : NULL;  // Betrag
    $user_name = $_SESSION['username'];  // Benutzername aus der Session holen

    try {
        // Fahrzeugdaten abrufen
        $sql_select = "SELECT * FROM vehicles WHERE id = ?";
        $stmt_select = $conn->prepare($sql_select);
        $stmt_select->execute([$vehicle_id]);
        $old_vehicle = $stmt_select->fetch(PDO::FETCH_ASSOC);

        if (!$old_vehicle) {
            echo json_encode(['success' => false, 'message' => 'Fahrzeug nicht gefunden.']);
            exit;
        }

        // Log-Eintrag für Fahrzeugänderung (nur die Änderung der Tanken-Daten)
        $changes = [];
        if ($fuel_checked) {
            $changes[] = "Getankt: Ja";
        } else {
            $changes[] = "Getankt: Nein";
        }
        if ($fuel_location !== $old_vehicle['fuel_location']) {
            $changes[] = "Wo getankt: " . $old_vehicle['fuel_location'] . " -> " . $fuel_location;
        }

        // Wenn Änderungen in den Tanken-Daten vorliegen, Eintrag ins Log
        if (!empty($changes)) {
            $action = "Fahrzeug Tanken ($license_plate) (" . implode(", ", $changes) . ")";  // Hier wird das Kennzeichen korrekt gesetzt
            $log_sql = "INSERT INTO vehicles_logs (vehicle_id, action, user_name) VALUES (?, ?, ?)";
            $log_stmt = $conn->prepare($log_sql);
            $log_stmt->execute([$vehicle_id, $action, $user_name]);
        }

        // Wenn der Haken gesetzt ist (Deckel bezahlt), Eintrag in die Deckel-Tabelle
        if ($fuel_checked) {
            $sql_deckel = "INSERT INTO deckel (vehicle_id, notiz, betrag, erstellt_von) 
                           VALUES (?, ?, ?, ?)";
            $stmt_deckel = $conn->prepare($sql_deckel);
            $stmt_deckel->execute([ $vehicle_id, "Getankt Kennzeichen: $license_plate in $fuel_location", $fuel_amount, $user_name ]);
        } else {
            // Eintrag in die Finanz-Tabelle (wenn direkt vom Firmenkonto abgegangen)
            $sql_finance = "INSERT INTO finanzen (typ, kategorie, notiz, betrag, erstellt_von) 
                            VALUES ('Ausgabe', 'Tanken', ?, ?, ?)";
            $stmt_finance = $conn->prepare($sql_finance);
            $stmt_finance->execute([ "Getankt Kennzeichen: $license_plate in $fuel_location", $fuel_amount, $user_name ]);
        }

        // Erfolgreiche Antwort zurückgeben
        echo json_encode(['success' => true, 'message' => 'Tanken-Daten erfolgreich aktualisiert.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Aktualisieren: ' . $e->getMessage()]);
    }
}
?>
