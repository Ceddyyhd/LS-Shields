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

// Überprüfen, ob die Anonym-Checkbox aktiviert ist
$erstellt_von = (isset($_POST['fuel_checked']) && $_POST['fuel_checked'] === 'true') ? 'Anonym' : $_SESSION['username'];

// Formulardaten auslesen
$vorschlag = $_POST['vorschlag'] ?? ''; // Vorschlag
$betreff = $_POST['betreff'] ?? ''; // Betreff
$bereich = $_POST['bereich'] ?? ''; // Bereich
$status = 'Eingetroffen'; // Standardstatus
$datum_uhrzeit = date('Y-m-d H:i:s'); // Aktuelles Datum und Uhrzeit

// Sicherstellen, dass der Betreff ausgefüllt wurde
if (empty($betreff)) {
    echo json_encode(['success' => false, 'message' => 'Betreff muss ausgefüllt werden.']);
    exit;
}

try {
    // SQL zum Einfügen des Verbesserungsvorschlags in die Datenbank
    $sql = "INSERT INTO verbesserungsvorschlaege (vorschlag, betreff, bereich, datum_uhrzeit, status, erstellt_von)
            VALUES (:vorschlag, :betreff, :bereich, :datum_uhrzeit, :status, :erstellt_von)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':vorschlag' => $vorschlag,
        ':betreff' => $betreff,
        ':bereich' => $bereich, // Der Bereich wird hier hinzugefügt
        ':datum_uhrzeit' => $datum_uhrzeit,
        ':status' => $status,
        ':erstellt_von' => $erstellt_von, // Ersteller ist auch der Name (oder Anonym)
    ]);

    // Log-Eintrag für die Erstellung des Vorschlags
    logAction('CREATE', 'verbesserungsvorschlaege', 'Vorschlag erstellt: Betreff: ' . $betreff . ', erstellt von: ' . $_SESSION['user_id']);

    echo json_encode(['success' => true, 'message' => 'Vorschlag wurde erfolgreich erstellt.']);
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
