<?php

include 'db.php';  // Datenbankverbindung einbinden

if (isset($_GET['id'])) {
    $vacation_id = $_GET['id'];

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
    } else {
        echo json_encode(['success' => false, 'message' => 'Urlaubsantrag nicht gefunden.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Keine ID übergeben.']);
}
?>
