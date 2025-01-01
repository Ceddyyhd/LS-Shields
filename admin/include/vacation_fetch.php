<?php
include 'db.php';  // Verbindung zur Datenbank

if (isset($_GET['id'])) {
    $vacationId = $_GET['id'];

    // SQL-Abfrage, um den Urlaubsantrag mit der entsprechenden ID zu holen
    $query = "SELECT v.*, u.name as employee_name
              FROM vacations v
              JOIN users u ON v.user_id = u.id
              WHERE v.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$vacationId]);
    
    $vacation = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($vacation) {
        // Rückgabe der Urlaubsantragsdaten als JSON
        echo json_encode([
            'success' => true,
            'id' => $vacation['id'],
            'user_id' => $vacation['user_id'],
            'start_date' => $vacation['start_date'],
            'end_date' => $vacation['end_date'],
            'status' => $vacation['status'],
            'note' => $vacation['note']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Urlaubsantrag nicht gefunden']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Fehlende ID']);
}
?>
