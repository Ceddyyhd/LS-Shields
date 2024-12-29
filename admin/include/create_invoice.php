<?php
// Include für Datenbankverbindung
include('db_connection.php');

// Funktion zum Generieren einer zufälligen 5-stelligen Rechnungsnummer
function generateInvoiceNumber() {
    return strtoupper(substr(md5(uniqid(rand(), true)), 0, 5));
}

// Daten aus dem AJAX-Formular
$kundenId = 1;  // Hier solltest du die Kunden-ID aus der Sitzung oder URL holen
$beschreibung = $_POST['beschreibung'];
$stueckpreis = $_POST['stueckpreis'];
$anzahl = $_POST['anzahl'];
$rabatt = $_POST['rabatt'];

// Rechnungsnummer generieren
$rechnungsnummer = generateInvoiceNumber();
$passwort = password_hash(uniqid(), PASSWORD_DEFAULT);  // Generiere ein sicheres Passwort

try {
    // Datenbankverbindung
    $pdo = new PDO('mysql:host=localhost;dbname=deineDatenbank', 'deinBenutzer', 'deinPasswort');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
    echo "Rechnung wurde erfolgreich erstellt! <br>";
    echo "Rechnungsnummer: $rechnungsnummer <br>";

} catch (Exception $e) {
    // Fehlerbehandlung und Rollback bei Fehler
    $pdo->rollBack();
    echo "Fehler: " . $e->getMessage();
}
?>
