<?php
include 'db.php';
session_start();

$logs_per_page = 25;  // Logs pro Seite
$page = isset($_GET['page']) ? $_GET['page'] : 1;  // Aktuelle Seite aus der URL holen, Standardwert ist 1
$offset = ($page - 1) * $logs_per_page;  // Berechne den Offset

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Ungültiges CSRF-Token']);
    exit;
}

// SQL-Query, um die Logs für die aktuelle Seite zu holen
$sql_logs = "SELECT * FROM vehicles_logs ORDER BY timestamp DESC LIMIT $logs_per_page OFFSET $offset";
$stmt = $conn->prepare($sql_logs);

try {
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Gesamtzahl der Logs für die Pagination
    $sql_count = "SELECT COUNT(*) AS total FROM vehicles_logs";
    $stmt_count = $conn->prepare($sql_count);
    $stmt_count->execute();
    $count = $stmt_count->fetch(PDO::FETCH_ASSOC);
    $total_logs = $count['total'];

    // Gesamtzahl der Seiten berechnen
    $total_pages = ceil($total_logs / $logs_per_page);

    // Logs und Seitenzahl als JSON zurückgeben
    echo json_encode([
        'logs' => $logs,
        'total_pages' => $total_pages
    ]);

    // Log-Eintrag für das Abrufen der Fahrzeug-Logs
    logAction('FETCH', 'vehicles_logs', 'Fahrzeug-Logs abgerufen, Seite: ' . $page . ', abgerufen von: ' . $_SESSION['user_id']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
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
