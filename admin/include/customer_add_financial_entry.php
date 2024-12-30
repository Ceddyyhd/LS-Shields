<?php
include 'db.php'; // Deine PDO-Datenbankverbindung

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $typ = $_POST['typ'];  // 'Einnahme' oder 'Ausgabe'
    $kategorie = $_POST['kategorie'];
    $notiz = $_POST['notiz'];
    $betrag = $_POST['betrag'];
    $erstellt_von = $_POST['erstellt_von'];  // Benutzername des angemeldeten Benutzers

    try {
        // Einfügen der Finanzdaten in die Tabelle
        $sql = "INSERT INTO finanzen (typ, kategorie, notiz, betrag, erstellt_von) 
                VALUES (:typ, :kategorie, :notiz, :betrag, :erstellt_von)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':typ', $typ);
        $stmt->bindParam(':kategorie', $kategorie);
        $stmt->bindParam(':notiz', $notiz);
        $stmt->bindParam(':betrag', $betrag);
        $stmt->bindParam(':erstellt_von', $erstellt_von);
        $stmt->execute();

        // Erfolg zurückgeben
        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        // Fehlerbehandlung
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
