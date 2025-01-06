<?php
require 'db.php';  // Deine DB-Verbindung

// Überprüfen, ob die Berechtigung vorhanden ist
if (isset($_SESSION['permissions']['eventplanung_delete']) && $_SESSION['permissions']['eventplanung_delete']) {

    // Sicherstellen, dass eine Event-ID übergeben wurde
    if (isset($_POST['event_id'])) {
        $event_id = (int) $_POST['event_id'];

        // SQL-Abfrage, um den Status des Events auf 'Gelöscht' zu setzen
        $sql = "UPDATE eventplanung SET status = 'Gelöscht' WHERE id = :event_id";

        // Vorbereiten und Ausführen der SQL-Abfrage
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Erfolgreiche Antwort zurückgeben
            echo json_encode(['success' => true, 'message' => 'Event erfolgreich gelöscht']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Fehler beim Löschen des Events']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Keine Event-ID übergeben']);
    }
} else {
    // Falls der Benutzer nicht die Berechtigung hat, eine Aktion auszuführen
    echo json_encode(['success' => false, 'message' => 'Keine Berechtigung für diese Aktion']);
}
?>
