<?php
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

    try {
        // Vorherige Fahrzeugdaten abrufen
        $sql_select = "SELECT * FROM vehicles WHERE id = ?";
        $stmt_select = $conn->prepare($sql_select);
        $stmt_select->execute([$vehicle_id]);
        $old_vehicle = $stmt_select->fetch(PDO::FETCH_ASSOC);

        // Fahrzeugdaten in der DB aktualisieren
        $sql_update = "UPDATE vehicles SET model = ?, license_plate = ?, location = ?, next_inspection = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->execute([$model, $license_plate, $location, $next_inspection, $vehicle_id]);

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

        // Log-Eintrag für die Änderungen erstellen
        $action = "Fahrzeug bearbeitet ($license_plate) (" . implode(", ", $changes) . ")";  // Änderungen in der gewünschten Formatierung zusammenfügen

        // Log-Eintrag in die vehicle_logs-Tabelle einfügen
        $log_sql = "INSERT INTO vehicles_logs (vehicle_id, action, user_name) VALUES (?, ?, ?)";
        $log_stmt = $conn->prepare($log_sql);
        $log_stmt->execute([$vehicle_id, $action, $user_name]);  // Benutzername zum Log hinzufügen

        // Erfolgsantwort zurückgeben
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        // Fehlerbehandlung
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
