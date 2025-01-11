<?php
include 'db.php'; // Datenbankverbindung

session_start(); // Sitzung starten

header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header('Location: ../error.php');
    exit;
}

// Hole den Benutzernamen aus der Session (falls nicht über AJAX übermittelt)
$erstellt_von = $_SESSION['username'] ?? 'Unbekannt';

// Formulardaten auslesen
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$nummer = filter_input(INPUT_POST, 'nummer', FILTER_SANITIZE_STRING);
$anfrage = filter_input(INPUT_POST, 'anfrage', FILTER_SANITIZE_STRING);
$status = 'Eingetroffen'; // Status immer auf "Eingetroffen" setzen
$datum_uhrzeit = date('Y-m-d H:i:s'); // Aktuelles Datum und Uhrzeit

// Validierung der Formulardaten
if (empty($name) || empty($nummer) || empty($anfrage)) {
    echo json_encode(['success' => false, 'message' => 'Alle Felder müssen ausgefüllt werden!']);
    exit;
}

try {
    // SQL zum Einfügen der Anfrage in die Datenbank
    $sql = "INSERT INTO anfragen (vorname_nachname, telefonnummer, anfrage, datum_uhrzeit, status, erstellt_von)
            VALUES (:name, :nummer, :anfrage, :datum_uhrzeit, :status, :erstellt_von)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':name' => $name,
        ':nummer' => $nummer,
        ':anfrage' => $anfrage,
        ':datum_uhrzeit' => $datum_uhrzeit,
        ':status' => $status,
        ':erstellt_von' => $erstellt_von, // Der Benutzername wird hier gespeichert
    ]);

    // Hole die ID der neu eingefügten Anfrage
    $anfrage_id = $conn->lastInsertId(); // Letzte eingefügte ID (Anfrage-ID)

    // Loggen des Eintrags
    logAction('INSERT', 'anfragen', 'anfrage_id: ' . $anfrage_id . ', erstellt_von: ' . $erstellt_von);

    // Erfolgreiche Antwort zurückgeben
    echo json_encode(['success' => true, 'message' => 'Anfrage wurde erfolgreich erstellt.']);
} catch (PDOException $e) {
    // Fehler bei der Datenbankoperation
    error_log('Database error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
    exit;
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
