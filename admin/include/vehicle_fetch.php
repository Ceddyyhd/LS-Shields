<?php
include 'db.php';  // Datenbankverbindung einbinden
header('Content-Type: application/json');  // Den Antworttyp auf JSON setzen

if (isset($_GET['vehicle_id'])) {
    $vehicle_id = $_GET['vehicle_id'];

    // Fahrzeugdaten aus der Datenbank holen
    $sql = "SELECT * FROM vehicles WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$vehicle_id]);
    $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($vehicle) {
        echo json_encode($vehicle);  // Fahrzeugdaten als JSON zurückgeben
    } else {
        echo json_encode(['error' => 'Fahrzeug nicht gefunden']);  // Wenn kein Fahrzeug gefunden wurde
    }
} else {
    echo json_encode(['error' => 'Fahrzeug ID fehlt']);  // Wenn keine ID übergeben wurde
}
?>
