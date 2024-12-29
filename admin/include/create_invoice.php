<?php
// Include für die Datenbankverbindung
include('db.php');  // Stellt sicher, dass $pdo richtig gesetzt ist

// Funktion zum Generieren einer zufälligen 5-stelligen Rechnungsnummer
function generateInvoiceNumber() {
    return strtoupper(substr(md5(uniqid(rand(), true)), 0, 5));
}

// Daten aus dem AJAX-Formular
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
    $pdo->beginTransaction();

    // Rechnungsdatensatz einfügen
    $stmt = $pdo->prepare("INSERT INTO Rechnungen (kunden_id, rechnungsnummer, passwort, rabatt) VALUES (?, ?, ?, ?)");
    $stmt->execute([$kundenId, $rechnungsnummer, $passwort, $rabatt]);

    // Die einzelnen Rechnungspositionen einfügen
    $invoiceId = $pdo->lastInsertId();
    foreach ($beschreibung as $index => $desc) {
        $stmt = $pdo->prepare("INSERT INTO Rechnungspositionen (rechnung_id, beschreibung, stueckpreis, anzahl) VALUES (?, ?, ?, ?)");
        $stmt->execute([$invoiceId, $desc, $stueckpreis[$index], $anzahl[$index]]);
    }

    // Transaktion abschließen
    $pdo->commit();

    // Erfolgsantwort
    echo json_encode([
        'status' => 'success',
        'message' => 'Rechnung wurde erfolgreich erstellt!',
        'rechnungsnummer' => $rechnungsnummer
    ]);

} catch (Exception $e) {
    // Fehlerbehandlung und Rollback bei Fehler
    $pdo->rollBack();
    echo json_encode([
        'status' => 'error',
        'message' => 'Fehler: ' . $e->getMessage()
    ]);
}
?>
