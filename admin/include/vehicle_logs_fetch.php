<?php
include 'db.php';

$logs_per_page = 25;  // Logs pro Seite
$page = isset($_GET['page']) ? $_GET['page'] : 1;  // Aktuelle Seite aus der URL holen, Standardwert ist 1
$offset = ($page - 1) * $logs_per_page;  // Berechne den Offset

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
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>
