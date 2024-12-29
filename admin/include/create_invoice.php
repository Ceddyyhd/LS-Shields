<?php
// Include für die Datenbankverbindung
include('db.php');  // Dies wird $conn aus db.php einbinden

// Funktion zum Generieren einer zufälligen 5-stelligen Rechnungsnummer
function generateInvoiceNumber() {
    return strtoupper(substr(md5(uniqid(rand(), true)), 0, 5));
}

// Überprüfen, ob die Kunden-ID gesetzt wurde und gültig ist
if (!isset($_POST['kunden_id']) || empty($_POST['kunden_id'])) {
    die(json_encode(['status' => 'error', 'message' => 'Fehler: Keine gültige Kunden-ID vorhanden.']));
}

$kundenId = $_POST['kunden_id'];  // Kunden-ID aus dem Formular
$beschreibung = $_POST['beschreibung'];  // Rechnungspositionen (Beschreibung)
$stueckpreis = $_POST['stueckpreis'];    // Stückpreise der Rechnungspositionen
$anzahl = $_POST['anzahl'];              // Anzahl der Artikel in den Rechnungspositionen
$rabatt = $_POST['rabatt'];              // Rabatt auf die gesamte Rechnung

// Rechnungsnummer generieren
$rechnungsnummer = generateInvoiceNumber();
$passwort = password_hash(uniqid(), PASSWORD_DEFAULT);  // Generiere ein sicheres Passwort

try {
    // Transaktion starten
    $conn->beginTransaction();

    // Rechnungsdatensatz einfügen
    $stmt = $conn->prepare("INSERT INTO Rechnungen (kunden_id, rechnungsnummer, passwort, rabatt) VALUES (?, ?, ?, ?)");
    $stmt->execute([$kundenId, $rechnungsnummer, $passwort, $rabatt]);

    // Die einzelnen Rechnungspositionen einfügen
    $invoiceId = $conn->lastInsertId();
    foreach ($beschreibung as $index => $desc) {
        $stmt = $conn->prepare("INSERT INTO Rechnungspositionen (rechnung_id, beschreibung, stueckpreis, anzahl) VALUES (?, ?, ?, ?)");
        $stmt->execute([$invoiceId, $desc, $stueckpreis[$index], $anzahl[$index]]);
    }

    // Transaktion abschließen
    $conn->commit();

    // Erfolgsantwort
    echo json_encode([
        'status' => 'success',
        'message' => 'Rechnung wurde erfolgreich erstellt!',
        'rechnungsnummer' => $rechnungsnummer
    ]);

} catch (Exception $e) {
    // Fehlerbehandlung und Rollback bei Fehler
    $conn->rollBack();
    echo json_encode([
        'status' => 'error',
        'message' => 'Fehler: ' . $e->getMessage()
    ]);
}
?>
