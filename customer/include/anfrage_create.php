<?php
include 'db.php'; // Datenbankverbindung

session_start(); // Sitzung starten

if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Benutzer ist nicht eingeloggt.']);
    exit;
}

// Benutzernamen und Benutzer-ID aus der Session holen
$erstellt_von = $_SESSION['username'];
$user_id = $_SESSION['user_id'];

// Formulardaten auslesen
$name = $_POST['name'] ?? '';
$nummer = $_POST['nummer'] ?? '';
$anfrage = $_POST['anfrage'] ?? '';
$status = 'Eingetroffen'; // Standardstatus
$datum_uhrzeit = date('Y-m-d H:i:s'); // Aktuelles Datum und Uhrzeit

// Validierung der Formulardaten
if (empty($name) || empty($nummer) || empty($anfrage)) {
    echo json_encode(['success' => false, 'message' => 'Alle Felder müssen ausgefüllt werden!']);
    exit;
}

try {
    // SQL zum Einfügen der Anfrage in die Datenbank
    $sql = "INSERT INTO anfragen (vorname_nachname, telefonnummer, anfrage, datum_uhrzeit, status, erstellt_von, kunden_id)
            VALUES (:name, :nummer, :anfrage, :datum_uhrzeit, :status, :erstellt_von, :user_id)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':name' => $name,
        ':nummer' => $nummer,
        ':anfrage' => $anfrage,
        ':datum_uhrzeit' => $datum_uhrzeit,
        ':status' => $status,
        ':erstellt_von' => $erstellt_von, // Der Benutzername wird hier gespeichert
        ':user_id' => $user_id, // Die Benutzer-ID wird hier gespeichert
    ]);

    echo json_encode(['success' => true, 'message' => 'Anfrage wurde erfolgreich erstellt.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>
