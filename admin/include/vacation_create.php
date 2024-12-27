<?php
include 'db.php'; // Datenbankverbindung

session_start(); // Sitzung starten

// Überprüfen, ob der Benutzer eingeloggt ist
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Benutzer ist nicht eingeloggt.']);
    exit;
}

// Benutzernamen aus der Session holen
$erstellt_von = $_SESSION['username'];

// Formulardaten auslesen
$start_date = $_POST['start_date'] ?? '';
$end_date = $_POST['end_date'] ?? '';

// Überprüfen, ob die Felder ausgefüllt sind
if (!$start_date || !$end_date) {
    echo json_encode(['success' => false, 'message' => 'Startdatum und Enddatum müssen ausgefüllt sein.']);
    exit;
}

// Status basierend auf der Dauer des Urlaubs setzen
$start_timestamp = strtotime($start_date);
$end_timestamp = strtotime($end_date);
$days_diff = ($end_timestamp - $start_timestamp) / (60 * 60 * 24);

$status = $days_diff <= 6 ? 'approved' : 'pending'; // Wenn der Urlaub 6 Tage oder weniger dauert, wird der Status auf "approved" gesetzt

// Das aktuelle Datum und Uhrzeit für die Erstellung
$datum_uhrzeit = date('Y-m-d H:i:s');

try {
    // SQL zum Einfügen des Urlaubs in die Datenbank
    $sql = "INSERT INTO vacations (start_date, end_date, status, erstellt_von, datum_uhrzeit)
            VALUES (:start_date, :end_date, :status, :erstellt_von, :datum_uhrzeit)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':start_date'   => $start_date,
        ':end_date'     => $end_date,
        ':status'       => $status,
        ':erstellt_von' => $erstellt_von,
        ':datum_uhrzeit'=> $datum_uhrzeit
    ]);

    echo json_encode(['success' => true, 'message' => 'Urlaub wurde erfolgreich erstellt.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>
