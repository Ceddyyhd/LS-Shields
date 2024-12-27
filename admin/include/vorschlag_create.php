<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? null;
    $vorschlag = $_POST['vorschlag'] ?? null;
    $status = $_POST['status'] ?? 'Eingetroffen';  // Standardstatus "Eingetroffen"
    $erstellt_von = $_POST['erstellt_von'] ?? 'Admin';  // Standard Ersteller, hier "Admin"

    // Berechtigungsprüfung
    if (!($_SESSION['permissions']['edit_employee'] ?? false)) {
        echo json_encode(['success' => false, 'message' => 'Keine Berechtigung, Vorschläge zu erstellen.']);
        exit;
    }

    if (!$name || !$nummer || !$vorschlag) {
        echo json_encode(['success' => false, 'message' => 'Bitte alle Felder ausfüllen.']);
        exit;
    }

    try {
        // Eintrag in die Datenbank erstellen
        $stmt = $conn->prepare("INSERT INTO verbesserungsvorschlaege (name, telefonnummer, vorschlag, status, erstellt_von) 
                                VALUES (:name, :vorschlag, :status, :erstellt_von)");
        $stmt->execute([
            ':name' => $name,
            ':vorschlag' => $vorschlag,
            ':status' => $status,
            ':erstellt_von' => $erstellt_von
        ]);

        // Erfolgreiche Antwort zurückgeben
        echo json_encode(['success' => true, 'message' => 'Verbesserungsvorschlag erfolgreich erstellt.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Erstellen: ' . $e->getMessage()]);
    }
}
?>
