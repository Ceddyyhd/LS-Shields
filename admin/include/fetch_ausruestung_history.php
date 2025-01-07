<?php
// Datenbankverbindung einbinden
include 'db.php';

// Überprüfen, ob die ID der Ausrüstung übergeben wurde
if (isset($_GET['id'])) {
    $ausruestungId = (int) $_GET['id'];

    // Abfrage zur Auswahl der Historie für die angegebene Ausrüstung
    $sql = "SELECT * FROM ausruestung_history WHERE key_name = :key_name ORDER BY timestamp DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':key_name' => $ausruestungId]);

    // Alle Historie-Einträge abrufen
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Wenn Historie vorhanden ist, geben Sie sie zurück
    if ($history) {
        echo json_encode($history);
    } else {
        echo json_encode(['success' => false, 'message' => 'Keine Historie gefunden']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Keine Ausrüstungs-ID übergeben']);
}
?>
