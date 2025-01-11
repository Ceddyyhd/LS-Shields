<?php
include 'db.php';  // Datenbankverbindung einbinden
session_start();

if (isset($_GET['id'])) {
    $vacation_id = $_GET['id'];

    // Überprüfen, ob das CSRF-Token gültig ist
    if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false, 'message' => 'Ungültiges CSRF-Token']);
        exit;
    }

    // Abrufen der Urlaubsdaten
    $sql = "SELECT * FROM vacations WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$vacation_id]);
    $vacation = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($vacation) {
        // Erfolgreiches Abrufen der Daten
        echo json_encode([
            'success' => true,
            'id' => $vacation['id'],
            'user_id' => $vacation['user_id'],
            'start_date' => $vacation['start_date'],
            'end_date' => $vacation['end_date'],
            'status' => $vacation['status'],
            'note' => $vacation['note']  // Notiz hinzufügen
        ]);

        // Log-Eintrag für das Abrufen der Urlaubsdaten
        logAction('FETCH', 'vacations', 'Urlaubsantrag abgerufen: ID: ' . $vacation_id . ', abgerufen von: ' . $_SESSION['user_id']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Urlaubsantrag nicht gefunden.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Keine ID übergeben.']);
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
