<?php
include 'security_check.php'; // Sicherheitsprüfung für diese Datei

// Datenbankverbindung einbinden
include 'db.php';

// Überprüfen, ob der key_name der Ausrüstung übergeben wurde
if (isset($_GET['id'])) {
    $key_name = $_GET['id'];  // Der key_name wird als Parameter übergeben

    // Abfrage zur Auswahl der Historie für den angegebenen key_name
    $sql = "SELECT * FROM ausruestung_history WHERE key_name = :key_name ORDER BY timestamp DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':key_name' => $key_name]);

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
