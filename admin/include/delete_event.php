<?php
include 'security_check.php'; // Sicherheitsprüfung für diese Datei

require 'db.php';  // Deine DB-Verbindung

// Überprüfen, ob die Berechtigung vorhanden ist

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
?>
