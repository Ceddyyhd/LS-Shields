<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vorschlag = $_POST['vorschlag'] ?? null;
    $status = $_POST['status'] ?? 'Eingetroffen';  // Standardstatus "Eingetroffen"
    $erstellt_von = $_SESSION['username'] ?? 'Unbekannt';  // Benutzernamen aus der Session holen

    // Berechtigungsprüfung
    if (!($_SESSION['permissions']['edit_employee'] ?? false)) {
        echo json_encode(['success' => false, 'message' => 'Keine Berechtigung, Vorschläge zu erstellen.']);
        exit;
    }

    if (!$vorschlag) {
        echo json_encode(['success' => false, 'message' => 'Bitte den Vorschlag ausfüllen.']);
        exit;
    }

    try {
        // Eintrag in die Datenbank erstellen
        // Hier setzen wir 'name' mit dem Wert von 'erstellt_von', wenn 'name' nicht entfernt werden kann.
        $stmt = $conn->prepare("INSERT INTO verbesserungsvorschlaege (name, vorschlag, status, erstellt_von) 
                                VALUES (:name, :vorschlag, :status, :erstellt_von)");
        $stmt->execute([
            ':name' => $erstellt_von,  // Setze 'name' auf 'erstellt_von'
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
