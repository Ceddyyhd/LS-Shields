<?php
include 'db.php'; // Datenbankverbindung einbinden

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $vacation_id = $_GET['id']; // Die ID des Urlaubsantrags

    try {
        // Abfrage der Urlaubsantragsdaten aus der Datenbank
        $sql = "SELECT * FROM vacations WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$vacation_id]);

        $vacation = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($vacation) {
            echo json_encode($vacation);
        } else {
            echo json_encode(['success' => false, 'message' => 'Urlaubsantrag nicht gefunden.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
    }
}
?>
