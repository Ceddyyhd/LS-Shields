<?php
include('db.php');

// Überprüfen, ob die Daten über POST gesendet wurden
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $umail = $_POST['umail'];
    $name = $_POST['name'];
    $nummer = $_POST['nummer'];
    $kontonummer = $_POST['kontonummer'];

    // SQL-Abfrage, um den neuen Kunden hinzuzufügen
    $stmt = $conn->prepare("INSERT INTO Kunden (umail, name, nummer, kontonummer) VALUES (?, ?, ?, ?)");
    $stmt->execute([$umail, $name, $nummer, $kontonummer]);

    echo json_encode(['status' => 'success', 'message' => 'Kunde erfolgreich erstellt!']);
}
?>
