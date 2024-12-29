<?php
include('db.php');

// Überprüfen, ob das Formular abgesendet wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kunden-Daten aus dem Formular holen
    $umail = $_POST['umail'];
    $name = $_POST['name'];
    $nummer = $_POST['nummer'];
    $kontonummer = $_POST['kontonummer'];

    // Prüfen, ob die E-Mail bereits existiert
    $stmt = $conn->prepare("SELECT * FROM Kunden WHERE umail = ?");
    $stmt->execute([$umail]);

    if ($stmt->rowCount() > 0) {
        die('Fehler: E-Mail existiert bereits!');
    }

    // SQL-Befehl zum Einfügen eines neuen Kunden
    $stmt = $conn->prepare("INSERT INTO Kunden (umail, name, nummer, kontonummer) VALUES (?, ?, ?, ?)");
    $stmt->execute([$umail, $name, $nummer, $kontonummer]);

    echo json_encode(['status' => 'success', 'message' => 'Kunde erfolgreich erstellt!']);
}
?>
