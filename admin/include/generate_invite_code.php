<?php
include 'db.php';  // Stellen Sie sicher, dass die Datenbankverbindung korrekt ist
session_start();
header('Content-Type: application/json');

// Funktion zum Generieren eines zufälligen Einladungscodes
function generateInviteCode($length = 10) {
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Überprüfen, ob das CSRF-Token gültig ist
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false, 'message' => 'Ungültiges CSRF-Token']);
        exit;
    }

    // Generiere den Einladungscode
    $invite_code = generateInviteCode();

    // Optional: Ablaufdatum für den Code setzen (z.B. 30 Tage ab heute)
    $expired_at = date('Y-m-d H:i:s', strtotime('+30 days'));

    // Den Einladungscode in die Datenbank einfügen
    try {
        $stmt = $conn->prepare("INSERT INTO invites (invite_code, expired_at) VALUES (:invite_code, :expired_at)");
        $stmt->execute([
            ':invite_code' => $invite_code,
            ':expired_at' => $expired_at
        ]);

        // Log-Eintrag für das Generieren des Einladungscodes
        logAction('INSERT', 'invites', 'invite_code: ' . $invite_code . ', created_by: ' . $_SESSION['user_id']);

        // Die neu eingefügten Daten zurückgeben
        echo json_encode([
            'success' => true,
            'invite_code' => $invite_code,
            'created_at' => date('Y-m-d H:i:s'),
            'expired_at' => $expired_at
        ]);
    } catch (Exception $e) {
        error_log('Fehler beim Generieren des Einladungscodes: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
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
