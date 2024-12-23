<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $ausruestung = $_POST['ausruestung'] ?? [];

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
        $stmt = $conn->prepare("SELECT key_name, status FROM benutzer_ausruestung WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        $existingItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $existingStatus = [];
        foreach ($existingItems as $item) {
            $existingStatus[$item['key_name']] = (int)$item['status'];
        }

        // Benutzername für das Log
        $editor_name = $_SESSION['username'] ?? 'Unbekannt';

        // Logs und Updates vorbereiten
        $logData = [];
        foreach ($existingStatus as $key_name => $currentStatus) {
            $newStatus = isset($ausruestung[$key_name]) ? (int)$ausruestung[$key_name] : 0;

            if ($newStatus !== $currentStatus) {
                $action = $newStatus ? 'hinzugefügt' : 'entfernt';
                $logData[] = [
                    'user_id' => $user_id,
                    'editor_name' => $editor_name,
                    'key_name' => $key_name,
                    'action' => $action,
                ];

                // Update oder Entfernen in der Datenbank
                if ($newStatus) {
                    $stmt = $conn->prepare("UPDATE benutzer_ausruestung 
                                            SET status = :status 
                                            WHERE user_id = :user_id AND key_name = :key_name");
                    $stmt->execute([
                        ':status' => $newStatus,
                        ':user_id' => $user_id,
                        ':key_name' => $key_name,
                    ]);
                } else {
                    $stmt = $conn->prepare("DELETE FROM benutzer_ausruestung 
                                            WHERE user_id = :user_id AND key_name = :key_name");
                    $stmt->execute([
                        ':user_id' => $user_id,
                        ':key_name' => $key_name,
                    ]);
                }
            }
        }

        // Neue Einträge hinzufügen, die vorher nicht existierten
        foreach ($ausruestung as $key_name => $status) {
            if (!array_key_exists($key_name, $existingStatus)) {
                $stmt = $conn->prepare("INSERT INTO benutzer_ausruestung (user_id, key_name, status) 
                                        VALUES (:user_id, :key_name, :status)");
                $stmt->execute([
                    ':user_id' => $user_id,
                    ':key_name' => $key_name,
                    ':status' => (int)$status,
                ]);

                $logData[] = [
                    'user_id' => $user_id,
                    'editor_name' => $editor_name,
                    'key_name' => $key_name,
                    'action' => 'hinzugefügt',
                ];
            }
        }

        // Logs in die Datenbank schreiben
        foreach ($logData as $log) {
            $stmt = $conn->prepare("INSERT INTO ausruestung_logs (user_id, editor_name, key_name, action) 
                                    VALUES (:user_id, :editor_name, :key_name, :action)");
            $stmt->execute([
                ':user_id' => $log['user_id'],
                ':editor_name' => $log['editor_name'],
                ':key_name' => $log['key_name'],
                ':action' => $log['action'],
            ]);
        }

        echo json_encode(['success' => true, 'message' => 'Änderungen gespeichert.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern: ' . $e->getMessage()]);
    }
    exit;
}
?>
