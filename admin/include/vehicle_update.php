<?php
include 'db.php';  // Datenbankverbindung einbinden

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vehicle_id = $_POST['vehicle_id'];
    $model = $_POST['model'];
    $license_plate = $_POST['license_plate'];
    $location = $_POST['location'];
    $next_inspection = $_POST['next_inspection'];

    try {
        // Fahrzeugdaten in der DB aktualisieren
        $sql = "UPDATE vehicles SET model = ?, license_plate = ?, location = ?, next_inspection = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$model, $license_plate, $location, $next_inspection, $vehicle_id]);

        // Log-Eintrag in die vehicle_logs-Tabelle einfügen
        $action = "Fahrzeug bearbeitet: $model ($license_plate)";
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
