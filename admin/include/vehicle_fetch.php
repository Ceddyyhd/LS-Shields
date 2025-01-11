<?php
include 'db.php';  // Datenbankverbindung einbinden
session_start();

$vehicle_id = isset($_GET['vehicle_id']) ? $_GET['vehicle_id'] : null;

if ($vehicle_id) {
    // Überprüfen, ob das CSRF-Token gültig ist
    if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false, 'message' => 'Ungültiges CSRF-Token']);
        exit;
    }

    try {
        $stmt = $conn->prepare("SELECT * FROM vehicles WHERE id = :vehicle_id");
        $stmt->execute([':vehicle_id' => $vehicle_id]);
        $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($vehicle) {
            echo json_encode($vehicle);  // Rückgabe der Fahrzeugdaten als JSON

            // Log-Eintrag für das Abrufen der Fahrzeugdaten
            logAction('FETCH', 'vehicles', 'Fahrzeug abgerufen: ID: ' . $vehicle_id . ', abgerufen von: ' . $_SESSION['user_id']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Fahrzeug nicht gefunden']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Keine Fahrzeug-ID angegeben']);
}

// Funktion zum Loggen von Aktionen
function logAction($action, $table, $details) {
    global $conn;

    // SQL-Abfrage zum Einfügen des Log-Eintrags
    $stmt = $conn->prepare("INSERT INTO logs (action, table_name, details, user_id, timestamp) VALUES (:action, :table_name, :details, :user_id, NOW())");
    $stmt->bindParam(':action', $action, PDO::PARAM_STR);
    $stmt->bindParam(':table_name', $table, PDO::PARAM_STR);
    $stmt->bindParam(':details', $details, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
}
?>
