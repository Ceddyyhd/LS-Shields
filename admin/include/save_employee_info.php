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

    // Überprüfen, ob das CSRF-Token gültig ist
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false, 'message' => 'Ungültiges CSRF-Token']);
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

        // Log-Eintrag für die Änderungen
        logAction('UPDATE', 'employee_info', 'user_id: ' . $user_id . ', changes: ' . json_encode(['waffenschein_type' => $waffenschein_type, 'fuehrerscheine' => $fuehrerscheine]) . ', edited_by: ' . $_SESSION['user_id']);

        echo json_encode(['success' => true, 'message' => 'Daten erfolgreich gespeichert.']);
    } catch (Exception $e) {
        error_log('Fehler beim Speichern der Daten: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern: ' . $e->getMessage()]);
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
