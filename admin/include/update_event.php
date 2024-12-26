<?php
// Event ID aus der URL holen
$event_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$event_id) {
    die("Kein Eventplanungs-ID angegeben.");
}

// Weiter mit der Datenbankabfrage
try {
    $stmt = $conn->prepare("SELECT * FROM eventplanung WHERE id = :id");
    $stmt->bindParam(':id', $event_id, PDO::PARAM_INT);
    $stmt->execute();
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        die('Eventplanung nicht gefunden.');
    }

    // Benutzer aus der `users`-Tabelle abfragen
    $userStmt = $conn->prepare("SELECT id, name FROM users");
    $userStmt->execute();
    $users = $userStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Fehler beim Abrufen der Daten: " . $e->getMessage());
}
?>
