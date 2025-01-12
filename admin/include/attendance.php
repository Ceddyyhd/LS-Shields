<?php

require_once 'db.php'; // Deine DB-Verbindung

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Überprüfen, ob die notwendigen POST-Daten vorhanden sind
    if (isset($_POST['user_id']) && isset($_POST['status'])) {
        $user_id = (int) $_POST['user_id']; // Benutzer-ID aus dem POST-Request
        $status = $_POST['status']; // Anwesenheitsstatus ('present' oder 'absent')

        // Validierung der Eingabedaten
        if (!in_array($status, ['present', 'absent'])) {
            echo json_encode(['success' => false, 'message' => 'Ungültiger Status']);
            exit;
        }

        try {
            // Zuerst den alten Anwesenheitseintrag löschen, wenn der Status auf 'absent' gesetzt wird
            if ($status == 'absent') {
                // Lösche den aktuellen Anwesenheitsdatensatz, wenn er existiert
                $deleteStmt = $conn->prepare("DELETE FROM attendance WHERE user_id = :user_id AND status = 'present'");
                $deleteStmt->bindParam(':user_id', $user_id);
                $deleteStmt->execute();
                
                // Überprüfe, ob der Löschen-Vorgang erfolgreich war
                if ($deleteStmt->rowCount() === 0) {
                    // Es wurde kein Eintrag gelöscht, möglicherweise gibt es keinen "present"-Eintrag
                    // Du kannst hier eine Nachricht oder einen Fehler loggen, wenn gewünscht
                }
            }

            // Einfügen des neuen Anwesenheitsdatensatzes (ob Abwesenheit oder Anwesenheit)
            $stmt = $conn->prepare("INSERT INTO attendance (user_id, status) VALUES (:user_id, :status)");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':status', $status);
            $stmt->execute();

            // Erfolgsantwort zurückgeben
            echo json_encode(['success' => true, 'message' => 'Status erfolgreich gespeichert']);
        } catch (PDOException $e) {
            // Fehlerausgabe im Falle eines SQL-Fehlers
            echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern: ' . $e->getMessage()]);
        }
    } else {
        // Fehlende Parameter
        echo json_encode(['success' => false, 'message' => 'Fehlende Parameter']);
    }
} else {
    // Nur POST-Anfragen werden akzeptiert
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage']);
}
?>
