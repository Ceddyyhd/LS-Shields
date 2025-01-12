<?php

include 'db.php';  // Datenbankverbindung einbinden
$vehicle_id = isset($_GET['vehicle_id']) ? $_GET['vehicle_id'] : null;

if ($vehicle_id) {
    try {
        $stmt = $conn->prepare("SELECT * FROM vehicles WHERE id = :vehicle_id");
        $stmt->execute([':vehicle_id' => $vehicle_id]);
        $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($vehicle) {
            echo json_encode($vehicle);  // Rückgabe der Fahrzeugdaten als JSON
        } else {
            echo json_encode(['success' => false, 'message' => 'Fahrzeug nicht gefunden']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Keine Fahrzeug-ID angegeben']);
}
?>
