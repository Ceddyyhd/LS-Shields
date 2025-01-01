<?php
include 'db.php';  // Datenbankverbindung einbinden

if (isset($_GET['vehicle_id'])) {
    $vehicle_id = $_GET['vehicle_id'];

    // Fahrzeugdaten abrufen
    $sql = "SELECT * FROM vehicles WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$vehicle_id]);
    $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($vehicle) {
        // JSON-Response zurückgeben
        echo json_encode($vehicle);
    } else {
        echo json_encode(['success' => false, 'message' => 'Fahrzeug nicht gefunden']);
    }
}
?>
