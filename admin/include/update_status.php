<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) $_POST['id'];
    $action = $_POST['action'] ?? '';

    if ($action === 'change_status') {
        // Status auf "in Bearbeitung" setzen
        $stmt = $conn->prepare("UPDATE anfragen SET status = 'in Bearbeitung' WHERE id = :id");
        $stmt->execute([':id' => $id]);
        echo json_encode(['success' => true, 'new_status' => 'in Bearbeitung']);
    } elseif ($action === 'move_to_eventplanung') {
        // Anfrage in eventplanung verschieben
        $stmt = $conn->prepare("SELECT * FROM anfragen WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $anfrage = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($anfrage) {
            $stmt = $conn->prepare("INSERT INTO eventplanung (vorname_nachname, telefonnummer, anfrage, datum_uhrzeit, status)
                                    VALUES (:vorname_nachname, :telefonnummer, :anfrage, :datum_uhrzeit, 'in Planung')");
            $stmt->execute([
                ':vorname_nachname' => $anfrage['vorname_nachname'],
                ':telefonnummer' => $anfrage['telefonnummer'],
                ':anfrage' => $anfrage['anfrage'],
                ':datum_uhrzeit' => $anfrage['datum_uhrzeit'],
            ]);

            // Aus Tabelle anfragen löschen
            $stmt = $conn->prepare("DELETE FROM anfragen WHERE id = :id");
            $stmt->execute([':id' => $id]);
            echo json_encode(['success' => true, 'removed' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Anfrage nicht gefunden']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Ungültige Aktion']);
    }
}
?>
