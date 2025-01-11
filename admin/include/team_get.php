<?php
include('db.php');
session_start();
header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['status' => 'error', 'message' => 'Ungültiges CSRF-Token']);
    exit;
}

// Überprüfen, ob eine Event-ID übergeben wurde
if (isset($_GET['event_id'])) {
    $eventId = $_GET['event_id'];

    try {
        // SQL-Abfrage, um die Team-Daten (als JSON) aus der eventplanung-Tabelle zu holen
        $query = "SELECT team_verteilung FROM eventplanung WHERE id = :event_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
        $stmt->execute();

        // Ergebnis abrufen
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Überprüfen, ob das Ergebnis vorhanden ist
        if ($result) {
            // JSON-Daten dekodieren
            $teamData = json_decode($result['team_verteilung'], true);

            // Überprüfen, ob die JSON-Daten dekodiert wurden
            if ($teamData === null) {
                error_log("Fehler beim Dekodieren der JSON-Daten.");
                echo json_encode(['status' => 'error', 'message' => 'Fehler beim Dekodieren der Team-Daten.']);
                exit;
            }

            // Debug: Ausgabe der Team-Daten
            error_log("Empfangene Team-Daten: " . print_r($teamData, true));

            // Gebe die dekodierten Team-Daten als JSON zurück
            echo json_encode($teamData);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Keine Eventplanungs-Daten gefunden.']);
        }

    } catch (PDOException $e) {
        // Fehler bei der Datenbankabfrage
        error_log('Datenbankfehler: ' . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Keine Event-ID angegeben']);
}
?>
