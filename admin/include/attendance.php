<?php
require_once 'db.php'; // Deine DB-Verbindung

session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Überprüfen, ob das CSRF-Token gültig ist
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header('Location: ../error.php');
        exit;
    }

    // Überprüfen, ob die notwendigen POST-Daten vorhanden sind
    if (isset($_POST['user_id']) && isset($_POST['status'])) {
        $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT); // Benutzer-ID aus dem POST-Request
        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING); // Anwesenheitsstatus ('present' oder 'absent')

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
                $deleteStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $deleteStmt->execute();
                
                // Überprüfe, ob der Löschen-Vorgang erfolgreich war
                if ($deleteStmt->rowCount() === 0) {
                    // Es wurde kein Eintrag gelöscht, möglicherweise gibt es keinen "present"-Eintrag
                    // Du kannst hier eine Nachricht oder einen Fehler loggen, wenn gewünscht
                }
            }

            // Einfügen des neuen Anwesenheitsdatensatzes (ob Abwesenheit oder Anwesenheit)
            $stmt = $conn->prepare("INSERT INTO attendance (user_id, status) VALUES (:user_id, :status)");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->execute();

            // Loggen des Eintrags
            logAction('INSERT', 'attendance', 'user_id: ' . $user_id . ', status: ' . $status);

            // Erfolgsantwort zurückgeben
            echo json_encode(['success' => true, 'message' => 'Status erfolgreich gespeichert']);
        } catch (PDOException $e) {
            // Fehlerausgabe im Falle eines SQL-Fehlers
            error_log('Fehler beim Speichern: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern: ' . $e->getMessage()]);
        }
    } else {
        // Fehlende Parameter
        echo json_encode(['success' => false, 'message' => 'Fehlende Parameter']);
    }
} else {
    header('Location: ../error.php');
    exit;
}

// Funktion zum Loggen von Aktionen
function logAction($action, $table, $details) {
    global $conn;

    // SQL-Abfrage zum Einfügen des Log-Eintrags
    $stmt = $conn->prepare("INSERT INTO logs (action, table_name, details, user_id, timestamp) VALUES (:action, :table_name, :details, :user_id, NOW())");
    $stmt->bindParam(':action', $action, PDO::PARAM_STR);
    $stmt->bindParam(':table_name', $table, PDO::PARAM_STR);
    $stmt->bindParam(':details', $details, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
}
?>
