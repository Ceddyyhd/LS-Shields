<?php
include 'db.php'; // Datenbankverbindung
session_start(); // Sitzung starten

// Überprüfen, ob der Benutzer eingeloggt ist
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Benutzer ist nicht eingeloggt.']);
    exit;
}

// Benutzernamen aus der Session holen, wenn nicht anonym
$erstellt_von = (isset($_POST['fuel_checked']) && $_POST['fuel_checked'] === 'true') ? 'Anonym' : $_SESSION['username'];

// Formulardaten auslesen
$vorschlag = $_POST['vorschlag'] ?? ''; // Vorschlag
$betreff = $_POST['betreff'] ?? ''; // Betreff
$status = 'Eingetroffen'; // Standardstatus
$datum_uhrzeit = date('Y-m-d H:i:s'); // Aktuelles Datum und Uhrzeit

try {
    // SQL zum Einfügen des Verbesserungsvorschlags in die Datenbank
    $sql = "INSERT INTO verbesserungsvorschlaege (vorschlag, name, datum_uhrzeit, status, erstellt_von, betreff)
            VALUES (:vorschlag, :name, :datum_uhrzeit, :status, :erstellt_von, :betreff)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':vorschlag' => $vorschlag,
        ':name' => $erstellt_von,  // Hier wird der Name hinzugefügt, entweder der Benutzername oder 'Anonym'
        ':datum_uhrzeit' => $datum_uhrzeit,
        ':status' => $status,
        ':erstellt_von' => $erstellt_von, // Ersteller ist auch der Name
        ':betreff' => $betreff // Der Betreff des Vorschlags
    ]);

    echo json_encode(['success' => true, 'message' => 'Vorschlag wurde erfolgreich erstellt.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>
