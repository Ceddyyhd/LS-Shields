<?php
include 'db.php'; // Datenbankverbindung

session_start(); // Sitzung starten

// Überprüfen, ob der Benutzer eingeloggt ist
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Benutzer ist nicht eingeloggt.']);
    exit;
}

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Ungültiges CSRF-Token']);
    exit;
}

// Benutzernamen aus der Session holen
$erstellt_von = $_SESSION['username'];

// Formulardaten auslesen
$start_date = $_POST['start_date'] ?? '';
$end_date = $_POST['end_date'] ?? '';

// Berechnen, ob der Status "approved" oder "pending" ist
$status = (strtotime($end_date) - strtotime($start_date) <= 4 * 86400) ? 'approved' : 'pending';  // Wenn Urlaub <= 6 Tage, dann genehmigt

// Der aktuelle Zeitpunkt wird automatisch von der Datenbank gesetzt
try {
    // SQL zum Einfügen der Anfrage in die Datenbank
    $sql = "INSERT INTO vacations (user_id, start_date, end_date, status, erstellt_von)
            VALUES (:user_id, :start_date, :end_date, :status, :erstellt_von)";

    // Bereite die Anfrage vor
    $stmt = $conn->prepare($sql);

    // Führe das SQL aus
    $stmt->execute([
        ':user_id' => $_SESSION['user_id'],  // Benutzer-ID aus der Session
        ':start_date' => $start_date,
        ':end_date' => $end_date,
        ':status' => $status,
        ':erstellt_von' => $erstellt_von,  // Der Benutzername wird hier gespeichert
    ]);

    // Log-Eintrag für die Erstellung des Urlaubsantrags
    logAction('CREATE', 'vacations', 'Urlaubsantrag erstellt: Startdatum: ' . $start_date . ', Enddatum: ' . $end_date . ', Status: ' . $status . ', erstellt von: ' . $_SESSION['user_id']);

    echo json_encode(['success' => true, 'message' => 'Urlaubsantrag erfolgreich erstellt.']);
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
