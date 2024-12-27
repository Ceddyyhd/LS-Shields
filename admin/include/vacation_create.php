<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';


    // Berechne die Dauer des Urlaubs (in Tagen)
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $interval = $start->diff($end);
    $vacation_duration = $interval->days + 1; // +1 um den letzten Tag mit einzubeziehen

    // Wenn der Urlaub weniger als 6 Tage dauert, wird er automatisch genehmigt
    $status = ($vacation_duration <= 6) ? 'approved' : 'pending';

    // Benutzername aus der Session holen (diesen Wert verwenden wir als 'erstellt_von')
    $erstellt_von = $_SESSION['username'] ?? 'Unknown';

    try {
        // SQL-Abfrage zum Einfügen des Urlaubsantrags
        $stmt = $conn->prepare("INSERT INTO vacations (user_id, start_date, end_date, status, erstellt_von)
                                VALUES (:user_id, :start_date, :end_date, :status, :erstellt_von)");

        // Hier muss die user_id je nach Benutzer in der Session zugewiesen werden (z.B. aus $_SESSION['user_id'])
        $user_id = $_SESSION['user_id'] ?? null; 

        if (!$user_id) {
            echo json_encode(['success' => false, 'message' => 'Benutzer ist nicht eingeloggt.']);
            exit;
        }

        // Führen Sie die SQL-Abfrage aus
        $stmt->execute([
            ':user_id' => $user_id,
            ':start_date' => $start_date,
            ':end_date' => $end_date,
            ':status' => $status,
            ':erstellt_von' => $erstellt_von
        ]);

        echo json_encode(['success' => true, 'message' => 'Urlaub wurde erfolgreich erstellt.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
    }
}
?>
