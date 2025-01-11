<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Überprüfen, ob das CSRF-Token gültig ist
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false, 'message' => 'Ungültiges CSRF-Token']);
        exit;
    }

    $user_id = $_POST['user_id'] ?? null;
    $ausbildungen = $_POST['ausbildungen'] ?? [];

    // Berechtigungsprüfung
    if (!($_SESSION['permissions']['edit_employee'] ?? false)) {
        echo json_encode(['success' => false, 'message' => 'Keine Berechtigung, Änderungen vorzunehmen.']);
        exit;
    }

    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'Benutzer-ID fehlt.']);
        exit;
    }

    try {
        // Abrufen der aktuellen Einträge in der Datenbank
        $stmt = $conn->prepare("SELECT ausbildung, status, bewertung FROM ausbildungen WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        $existingEntries = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $existingData = [];
        foreach ($existingEntries as $entry) {
            $existingData[$entry['ausbildung']] = [
                'status' => (int)$entry['status'],
                'bewertung' => (int)$entry['bewertung']
            ];
        }

        // Benutzername für das Log
        $stmt = $conn->prepare("SELECT name FROM users WHERE id = :user_id");
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $editor_name = $user['name'] ?? 'Unbekannt';

        // Logs und Updates vorbereiten
        $logData = [];
        foreach ($ausbildungen as $key_name => $data) {
            $newStatus = isset($data['status']) ? (int)$data['status'] : 0;
            $newRating = isset($data['rating']) ? (int)$data['rating'] : 0;

            $currentStatus = $existingData[$key_name]['status'] ?? 0;
            $currentRating = $existingData[$key_name]['bewertung'] ?? 0;

            // Nur aktualisieren, wenn sich der Status oder die Bewertung geändert hat
            if ($newStatus !== $currentStatus || $newRating !== $currentRating) {
                $stmt = $conn->prepare("REPLACE INTO ausbildungen (user_id, ausbildung, status, bewertung) VALUES (:user_id, :ausbildung, :status, :bewertung)");
                $stmt->execute([
                    ':user_id' => $user_id,
                    ':ausbildung' => $key_name,
                    ':status' => $newStatus,
                    ':bewertung' => $newRating
                ]);

                $logData[] = [
                    'ausbildung' => $key_name,
                    'old_status' => $currentStatus,
                    'new_status' => $newStatus,
                    'old_rating' => $currentRating,
                    'new_rating' => $newRating
                ];
            }
        }

        // Log-Eintrag für die Änderungen
        logAction('UPDATE', 'ausbildungen', 'user_id: ' . $user_id . ', changes: ' . json_encode($logData) . ', edited_by: ' . $editor_name);

        echo json_encode(['success' => true, 'message' => 'Ausbildungen erfolgreich aktualisiert.']);
    } catch (PDOException $e) {
        error_log('Fehler beim Aktualisieren der Ausbildungen: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Fehler beim Aktualisieren der Ausbildungen: ' . $e->getMessage()]);
    }
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
