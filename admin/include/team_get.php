<?php
include('db.php');

// Überprüfen, ob eine Event-ID übergeben wurde
if (isset($_GET['event_id'])) {
    $eventId = $_GET['event_id'];

    // Abfrage, um die Team-Daten zu holen
    $query = "SELECT t.id, t.team_name, t.area_name, t.employee_name, t.is_team_lead
              FROM team_assignments t
              WHERE t.event_id = :event_id";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':event_id', $eventId);
    $stmt->execute();

    // Ergebnisse abrufen
    $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Rückgabe der Team-Daten als JSON
    echo json_encode($teams);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Keine Event-ID angegeben']);
}
?>
