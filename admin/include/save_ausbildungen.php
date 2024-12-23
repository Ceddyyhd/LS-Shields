<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $ausbildungen = $_POST['ausbildungen'] ?? [];

    if (!($_SESSION['permissions']['edit_employee'] ?? false)) {
        echo json_encode(['success' => false, 'message' => 'Keine Berechtigung, Änderungen vorzunehmen.']);
        exit;
    }

    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'Benutzer-ID fehlt.']);
        exit;
    }

    try {
        $stmt = $conn->prepare("SELECT ausbildung, status FROM ausbildungen WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        $existingItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $existingStatus = [];
        foreach ($existingItems as $item) {
            $existingStatus[$item['ausbildung']] = (int)$item['status'];
        }

        // Benutzername für das Logging
        $stmt = $conn->prepare("SELECT name FROM users WHERE id = :user_id");
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $editor_name = $user['name'] ?? 'Unbekannt';

        // Änderungen und Logging
        $logData = [];
        foreach ($existingStatus as $key_name => $currentStatus) {
            $newStatus = isset($ausbildungen[$key_name]) ? (int)$ausbildungen[$key_name]['status'] : 0;

            if ($newStatus !== $currentStatus) {
                $action = $newStatus ? 'hinzugefügt' : 'entfernt';
                $logData[] = [
                    'user_id' => $user_id,
                    'editor_name' => $editor_name,
                    'ausbildung' => $key_name,
                    'action' => $action,
                ];

                if ($newStatus) {
                    $stmt = $conn->prepare("UPDATE ausbildungen SET status = :status, bewertung = :bewertung WHERE user_id = :user_id AND ausbildung = :ausbildung");
                    $stmt->execute([
                        ':status' => $newStatus,
                        ':bewertung' => $ausbildungen[$key_name]['rating'] ?? 0,
                        ':user_id' => $user_id,
                        ':ausbildung' => $key_name,
                    ]);
                } else {
                    $stmt = $conn->prepare("DELETE FROM ausbildungen WHERE user_id = :user_id AND ausbildung = :ausbildung");
                    $stmt->execute([
                        ':user_id' => $user_id,
                        ':ausbildung' => $key_name,
                    ]);
                }
            }
        }

        // Neue Einträge hinzufügen
        foreach ($ausbildungen as $key_name => $data) {
            if (!array_key_exists($key_name, $existingStatus)) {
                $stmt = $conn->prepare("INSERT INTO ausbildungen (user_id, ausbildung, status, bewertung) VALUES (:user_id, :ausbildung, :status, :bewertung)");
                $stmt->execute([
                    ':user_id' => $user_id,
                    ':ausbildung' => $key_name,
                    ':status' => (int)$data['status'],
                    ':bewertung' => (int)$data['rating'],
                ]);

                $logData[] = [
                    'user_id' => $user_id,
                    'editor_name' => $editor_name,
                    'ausbildung' => $key_name,
                    'action' => 'hinzugefügt',
                ];
            }
        }

        // Logs speichern
        foreach ($logData as $log) {
            $stmt = $conn->prepare("INSERT INTO ausbildung_logs (user_id, editor_name, ausbildung, action) VALUES (:user_id, :editor_name, :ausbildung, :action)");
            $stmt->execute([
                ':user_id' => $log['user_id'],
                ':editor_name' => $log['editor_name'],
                ':ausbildung' => $log['ausbildung'],
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
