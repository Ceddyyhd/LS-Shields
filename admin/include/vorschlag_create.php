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
$vorschlag = $_POST['vorschlag'] ?? '';  // Vorschlag
$status = 'Eingetroffen';  // Standardstatus
$datum_uhrzeit = date('Y-m-d H:i:s');  // Aktuelles Datum und Uhrzeit

try {
    // SQL zum Einfügen des Verbesserungsvorschlags in die Datenbank
    $sql = "INSERT INTO verbesserungsvorschlaege (vorschlag, datum_uhrzeit, status, erstellt_von)
            VALUES (:vorschlag, :datum_uhrzeit, :status, :erstellt_von)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':vorschlag' => $vorschlag,
        ':datum_uhrzeit' => $datum_uhrzeit,
        ':status' => $status,
        ':erstellt_von' => $erstellt_von,  // Der Benutzername wird hier gespeichert
    ]);

    echo json_encode(['success' => true, 'message' => 'Vorschlag wurde erfolgreich erstellt.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>
