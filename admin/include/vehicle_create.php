<?php
include 'db.php';  // Datenbankverbindung einbinden

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fahrzeugdaten aus dem POST holen
    $model = $_POST['model'];
    $license_plate = $_POST['license_plate'];
    $location = $_POST['location'];
    $next_inspection = $_POST['next_inspection'];

    try {
        // Fahrzeugdaten in die DB einfügen
        $sql = "INSERT INTO vehicles (model, license_plate, location, next_inspection) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$model, $license_plate, $location, $next_inspection]);

        // Log-Eintrag in die vehicle_logs-Tabelle einfügen
        $vehicle_id = $conn->lastInsertId();  // ID des neu eingefügten Fahrzeugs
        $action = "Fahrzeug hinzugefügt: $model ($license_plate)";
        $log_sql = "INSERT INTO vehicle_logs (vehicle_id, action) VALUES (?, ?)";
        $log_stmt = $conn->prepare($log_sql);
        $log_stmt->execute([$vehicle_id, $action]);

        // Erfolgsantwort zurückgeben
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        // Fehlerbehandlung
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
