<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

            // Prüfen, ob der Status oder die Bewertung geändert wurde
            if ($newStatus !== $currentStatus || $newRating !== $currentRating) {
                $action = '';
                if ($newStatus !== $currentStatus) {
                    $action = $newStatus ? 'hinzugefügt' : 'entfernt';
                } elseif ($newRating !== $currentRating) {
                    $action = 'geändert';
                }

                // Loggen der Änderung
                if ($action) {
                    $logData[] = [
                        'user_id' => $user_id,
                        'editor_name' => $editor_name,
                        'ausbildung' => $key_name,
                        'action' => $action,
                        'rating' => $newRating
                    ];
                }

                // Datenbank aktualisieren
                if ($newStatus || $newRating > 0) {
                    // Hinzufügen oder Aktualisieren
                    $stmt = $conn->prepare("INSERT INTO ausbildungen (user_id, ausbildung, status, bewertung) 
                                            VALUES (:user_id, :ausbildung, :status, :bewertung)
                                            ON DUPLICATE KEY UPDATE status = :status, bewertung = :bewertung");
                    $stmt->execute([
                        ':user_id' => $user_id,
                        ':ausbildung' => $key_name,
                        ':status' => $newStatus,
                        ':bewertung' => $newRating
                    ]);
                } else {
                    // Entfernen, wenn Status 0 und keine Bewertung vorhanden
                    $stmt = $conn->prepare("DELETE FROM ausbildungen WHERE user_id = :user_id AND ausbildung = :ausbildung");
                    $stmt->execute([
                        ':user_id' => $user_id,
                        ':ausbildung' => $key_name
                    ]);
                }
            }
        }

        // Logs in die Datenbank schreiben
        foreach ($logData as $log) {
            $stmt = $conn->prepare("INSERT INTO ausbildung_logs (user_id, editor_name, ausbildung, action, rating) 
                                    VALUES (:user_id, :editor_name, :ausbildung, :action, :rating)");
            $stmt->execute($log);
        }

        echo json_encode(['success' => true, 'message' => 'Änderungen gespeichert.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern: ' . $e->getMessage()]);
    }
    exit;
}
?>
