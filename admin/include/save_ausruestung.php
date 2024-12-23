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

    // Vorhandene Einträge abrufen
    $stmt = $conn->prepare("SELECT key_name, status FROM benutzer_ausruestung WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $existingItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $existingStatus = [];
    foreach ($existingItems as $item) {
        $existingStatus[$item['key_name']] = (int)$item['status'];
    }

    // Logs und Updates verarbeiten
    $logData = [];
    foreach ($ausruestung as $key_name => $status) {
        $status = (int)$status;
        if (!isset($existingStatus[$key_name]) || $existingStatus[$key_name] !== $status) {
            $action = $status ? 'hinzugefügt' : 'entfernt';
            $logData[] = [
                'user_id' => $user_id,
                'editor_name' => $editor_name,
                'key_name' => $key_name,
                'action' => $action
            ];
        }
    }

    // Logs speichern
    foreach ($logData as $log) {
        $stmt = $conn->prepare("INSERT INTO ausruestung_logs (user_id, editor_name, key_name, action) 
                                VALUES (:user_id, :editor_name, :key_name, :action)");
        $stmt->execute($log);
    }

    // Neue Einträge speichern
    $stmt = $conn->prepare("DELETE FROM benutzer_ausruestung WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);

    foreach ($ausruestung as $key_name => $status) {
        $stmt = $conn->prepare("INSERT INTO benutzer_ausruestung (user_id, key_name, status) VALUES (:user_id, :key_name, :status)");
        $stmt->execute([
            ':user_id' => $user_id,
            ':key_name' => $key_name,
            ':status' => $status
        ]);
    }

    echo json_encode(['success' => true, 'message' => 'Änderungen gespeichert.']);
    exit;
}
?>
