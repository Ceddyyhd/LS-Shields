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
        // Benutzername direkt aus der Session oder Datenbank holen
        if (!isset($_SESSION['username'])) {
            $stmt = $conn->prepare("SELECT name FROM users WHERE id = :user_id");
            $stmt->execute([':user_id' => $_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $editor_name = $user['name'] ?? 'Unbekannt';
        } else {
            $editor_name = $_SESSION['username'];
        }

        // Abrufen der aktuellen Einträge in der Datenbank
        $stmt = $conn->prepare("SELECT ausbildung, status, bewertung FROM benutzer_ausbildungen WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        $existingItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $existingStatus = [];
        $existingBewertungen = [];
        foreach ($existingItems as $item) {
            $existingStatus[$item['ausbildung']] = (int)$item['status'];
            $existingBewertungen[$item['ausbildung']] = (int)$item['bewertung'];
        }

        // Logs und Updates vorbereiten
        $logData = [];
        foreach ($existingStatus as $key_name => $currentStatus) {
            $newStatus = isset($ausbildungen[$key_name]['status']) ? (int)$ausbildungen[$key_name]['status'] : 0;
            $newBewertung = isset($ausbildungen[$key_name]['rating']) ? (int)$ausbildungen[$key_name]['rating'] : 0;

            // Änderungen überprüfen
            if ($newStatus !== $currentStatus || $newBewertung !== ($existingBewertungen[$key_name] ?? 0)) {
                $action = $newStatus ? 'hinzugefügt/aktualisiert' : 'entfernt';
                $logData[] = [
                    'user_id' => $user_id,
                    'editor_name' => $editor_name,
                    'ausbildung' => $key_name,
                    'status' => $newStatus,
                    'bewertung' => $newBewertung,
                    'action' => $action,
                ];

                if ($newStatus) {
                    // Update oder Hinzufügen
                    $stmt = $conn->prepare("INSERT INTO benutzer_ausbildungen (user_id, ausbildung, status, bewertung) 
                                            VALUES (:user_id, :ausbildung, :status, :bewertung)
                                            ON DUPLICATE KEY UPDATE status = :status, bewertung = :bewertung");
                    $stmt->execute([
                        ':user_id' => $user_id,
                        ':ausbildung' => $key_name,
                        ':status' => $newStatus,
                        ':bewertung' => $newBewertung,
                    ]);
                } else {
                    // Entfernen, wenn Status 0 ist
                    $stmt = $conn->prepare("DELETE FROM benutzer_ausbildungen 
                                            WHERE user_id = :user_id AND ausbildung = :ausbildung");
                    $stmt->execute([
                        ':user_id' => $user_id,
                        ':ausbildung' => $key_name,
                    ]);
                }
            }
        }

        // Neue Einträge hinzufügen, die vorher nicht existierten
        foreach ($ausbildungen as $key_name => $data) {
            if (!array_key_exists($key_name, $existingStatus)) {
                $status = (int)($data['status'] ?? 0);
                $rating = (int)($data['rating'] ?? 0);
                $stmt = $conn->prepare("INSERT INTO benutzer_ausbildungen (user_id, ausbildung, status, bewertung) 
                                        VALUES (:user_id, :ausbildung, :status, :bewertung)");
                $stmt->execute([
                    ':user_id' => $user_id,
                    ':ausbildung' => $key_name,
                    ':status' => $status,
                    ':bewertung' => $rating,
                ]);

                $logData[] = [
                    'user_id' => $user_id,
                    'editor_name' => $editor_name,
                    'ausbildung' => $key_name,
                    'status' => $status,
                    'bewertung' => $rating,
                    'action' => 'hinzugefügt',
                ];
            }
        }

        // Logs in die Datenbank schreiben
        foreach ($logData as $log) {
            $stmt = $conn->prepare("INSERT INTO ausbildung_logs (user_id, editor_name, ausbildung, status, bewertung, action) 
                                    VALUES (:user_id, :editor_name, :ausbildung, :status, :bewertung, :action)");
            $stmt->execute([
                ':user_id' => $log['user_id'],
                ':editor_name' => $log['editor_name'],
                ':ausbildung' => $log['ausbildung'],
                ':status' => $log['status'],
                ':bewertung' => $log['bewertung'],
                ':action' => $log['action'],
            ]);
        }

        echo json_encode(['success' => true, 'message' => 'Änderungen gespeichert.']);
    } catch (Exception $e) {
        error_log('Fehler in save_ausbildungen.php: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern: ' . $e->getMessage()]);
    }
    exit;
}
?>
