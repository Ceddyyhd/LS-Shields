<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $action = $_POST['action'] ?? null;

    // Berechtigungsprüfung
    if (!($_SESSION['permissions']['change_status'] ?? false)) {
        echo json_encode(['success' => false, 'message' => 'Keine Berechtigung, den Status zu ändern.']);
        exit;
    }

    if (!$id || !$action) {
        echo json_encode(['success' => false, 'message' => 'Fehlende Daten.']);
        exit;
    }

    try {
        // Überprüfen, ob der Vorschlag existiert
        $stmt = $conn->prepare("SELECT id, status FROM verbesserungsvorschlaege WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $vorschlag = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$vorschlag) {
            echo json_encode(['success' => false, 'message' => 'Vorschlag nicht gefunden.']);
            exit;
        }

        // Status ändern je nach Aktion
        if ($action === 'change_status' && $vorschlag['status'] === 'Eingetroffen') {
            // Ändere den Status zu "In Bearbeitung"
            $newStatus = 'In Bearbeitung';
        } elseif ($action === 'move_to_abgeschlossen' && $vorschlag['status'] === 'In Bearbeitung') {
            // Ändere den Status zu "Abgeschlossen"
            $newStatus = 'Abgeschlossen';
        } else {
            echo json_encode(['success' => false, 'message' => 'Unzulässige Statusänderung.']);
            exit;
        }

        // Status in der Datenbank aktualisieren
        $stmt = $conn->prepare("UPDATE verbesserungsvorschlaege SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $newStatus, ':id' => $id]);

        // Erfolgreiche Antwort zurückgeben
        echo json_encode(['success' => true, 'message' => 'Status erfolgreich geändert.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Aktualisieren: ' . $e->getMessage()]);
    }
}
?>
