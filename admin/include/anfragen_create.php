<?php
include 'db.php'; // Datenbankverbindung

session_start(); // Stelle sicher, dass die Sitzung gestartet wurde

// Überprüfe, ob der Benutzer eingeloggt ist
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Benutzer ist nicht eingeloggt.']);
    exit;
}

// Hole den Benutzernamen aus der Session
$erstellt_von = $_SESSION['username']; // Benutzernamen aus der Session holen

// Formulardaten auslesen
$name = $_POST['name'] ?? '';
$nummer = $_POST['nummer'] ?? '';
$anfrage = $_POST['anfrage'] ?? '';
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

    // Erfolgreiche Antwort zurückgeben
    echo json_encode(['success' => true, 'message' => 'Anfrage wurde erfolgreich erstellt.']);
} catch (PDOException $e) {
    // Fehler bei der Datenbankoperation
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>
