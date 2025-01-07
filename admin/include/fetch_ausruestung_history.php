<?php
// Fehlerprotokollierung aktivieren (für Debugging)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Datenbankverbindung einbinden
include 'db.php';

// Überprüfen, ob der Benutzer eingeloggt ist
session_start();

// Wenn der Benutzer nicht eingeloggt ist, dann nichts tun
if (!isset($_SESSION['user_id'])) {
    die("Kein Benutzer eingeloggt.");
}

// Empfangen der Ausrüstungs-ID
$id = $_GET['id'];  // Hier nehmen wir die ID der Ausrüstung, um deren Historie zu laden

try {
    // SQL-Abfrage, um alle Historie-Einträge für die angegebene Ausrüstung zu holen
    $stmt = $conn->prepare("SELECT * FROM ausruestung_history WHERE key_name = :key_name ORDER BY timestamp DESC");
    $stmt->execute([ ':key_name' => $id ]);
    
    // Alle Ergebnisse in einem Array speichern
    $historyEntries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Wenn keine Einträge vorhanden sind
    if ($historyEntries) {
        echo json_encode($historyEntries);
    } else {
        echo json_encode(['success' => false, 'message' => 'Keine Historie gefunden.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Fehler beim Abrufen der Historie: ' . $e->getMessage()]);
}
?>
