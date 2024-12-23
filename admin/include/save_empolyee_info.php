<?php
include 'db.php';
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $waffenschein_type = $_POST['waffenschein_type'] ?? 'none';
    $fuehrerscheine = $_POST['fuehrerscheine'] ?? [];

    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'Benutzer-ID fehlt.']);
        exit;
    }

    // Berechtigung prüfen
    if (!($_SESSION['permissions']['edit_employee'] ?? false)) {
        echo json_encode(['success' => false, 'message' => 'Keine Berechtigung.']);
        exit;
    }

    try {
        $stmt = $conn->prepare("
            INSERT INTO employee_info (user_id, waffenschein_type, fuehrerscheine)
            VALUES (:user_id, :waffenschein_type, :fuehrerscheine)
            ON DUPLICATE KEY UPDATE 
                waffenschein_type = :waffenschein_type_update,
                fuehrerscheine = :fuehrerscheine_update
        ");
        $stmt->execute([
            ':user_id' => $user_id,
            ':waffenschein_type' => $waffenschein_type,
            ':fuehrerscheine' => json_encode($fuehrerscheine),
            ':waffenschein_type_update' => $waffenschein_type,
            ':fuehrerscheine_update' => json_encode($fuehrerscheine),
        ]);

        echo json_encode(['success' => true, 'message' => 'Daten erfolgreich gespeichert.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern: ' . $e->getMessage()]);
    }
}
