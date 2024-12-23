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

    // Benutzername für das Log
    $editor_name = $_SESSION['username'] ?? 'Unbekannt';

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

        // Vorhandene Einträge aktualisieren oder neue hinzufügen
        foreach ($ausruestung as $key_name => $status) {
            $status = (int)$status;

            $stmt = $conn->prepare("INSERT INTO benutzer_ausruestung (user_id, key_name, status)
                                    VALUES (:user_id, :key_name, :status)
                                    ON DUPLICATE KEY UPDATE status = :status");
            $stmt->execute([
                ':user_id' => $user_id,
                ':key_name' => $key_name,
                ':status' => $status,
            ]);
        }

        echo json_encode(['success' => true, 'message' => 'Änderungen gespeichert.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern: ' . $e->getMessage()]);
    }
    exit;
}
?>
