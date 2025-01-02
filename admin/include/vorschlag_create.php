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
$bereich = $_POST['bereich'] ?? '';  // Bereich
$anonym = isset($_POST['anonym']) ? 1 : 0; // Checkbox für Anonymität
$betreff = $_POST['betreff'] ?? '';  // Betreff
$vorschlag = $_POST['vorschlag'] ?? ''; // Vorschlag
$status = 'Eingetroffen'; // Standardstatus
$datum_uhrzeit = date('Y-m-d H:i:s'); // Aktuelles Datum und Uhrzeit

// Überprüfen, ob alle erforderlichen Felder vorhanden sind
if (empty($bereich) || empty($betreff) || empty($vorschlag)) {
    echo json_encode(['success' => false, 'message' => 'Alle Felder müssen ausgefüllt werden!']);
    exit;
}

try {
    // SQL zum Einfügen des Verbesserungsvorschlags in die Datenbank
    $sql = "INSERT INTO verbesserungsvorschlaege (bereich, anonym, vorschlag, betreff, datum_uhrzeit, status, erstellt_von)
            VALUES (:bereich, :anonym, :vorschlag, :betreff, :datum_uhrzeit, :status, :erstellt_von)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':bereich' => $bereich,
        ':anonym' => $anonym,  // Anonymität als 1 oder 0 speichern
        ':vorschlag' => $vorschlag,
        ':betreff' => $betreff,
        ':datum_uhrzeit' => $datum_uhrzeit,
        ':status' => $status,
        ':erstellt_von' => $erstellt_von,
    ]);

    echo json_encode(['success' => true, 'message' => 'Vorschlag wurde erfolgreich erstellt.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>
