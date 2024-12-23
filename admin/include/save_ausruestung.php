<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $ausruestung = $_POST['ausruestung'] ?? [];

    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'Benutzer-ID fehlt.']);
        exit;
    }

    // Lösche alte Einträge
    $stmt = $conn->prepare("DELETE FROM benutzer_ausruestung WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);

    // Neue Einträge hinzufügen
    foreach ($ausruestung as $key_name => $status) {
        $stmt = $conn->prepare("INSERT INTO benutzer_ausruestung (user_id, key_name, status) VALUES (:user_id, :key_name, :status)");
        $stmt->execute([
            ':user_id' => $user_id,
            ':key_name' => $key_name,
            ':status' => (int)$status
        ]);
    }

    echo json_encode(['success' => true, 'message' => 'Daten gespeichert.']);
    exit;
}
?>
