<?php
include 'db.php'; // Datenbankverbindung einbinden

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Die ID des Urlaubsantrags wird über GET übergeben
    $vacation_id = $_GET['id'];

    try {
        // SQL-Abfrage, um die Daten des Urlaubsantrags aus der Datenbank abzurufen
        $sql = "SELECT v.*, u.name as employee_name 
                FROM vacations v
                JOIN users u ON v.user_id = u.id
                WHERE v.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$vacation_id]);

        // Wenn der Antrag gefunden wird, gebe die Daten als JSON zurück
        $vacation = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($vacation) {
            echo json_encode($vacation);
        } else {
            echo json_encode(['success' => false, 'message' => 'Urlaubsantrag nicht gefunden.']);
        }
    } catch (PDOException $e) {
        // Fehlerbehandlung bei der Datenbankabfrage
        echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
    }
}
?>
