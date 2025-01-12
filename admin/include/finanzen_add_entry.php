<?php
include 'security_check.php'; // Sicherheitsprüfung für diese Datei

session_start();
include 'db.php'; // Deine Datenbankverbindung

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Überprüfe, ob das unsichtbare Feld 'erstellt_von' gesetzt wurde
    if (isset($_POST['erstellt_von'])) {
        $erstellt_von = $_POST['erstellt_von'];
    } else {
        echo json_encode(["status" => "error", "message" => "Benutzer nicht angegeben."]);
        exit();
    }

    // Eingabewerte aus dem Formular holen
    $typ = $_POST['typ'];
    $kategorie = $_POST['kategorie'];
    $notiz = $_POST['notiz'];
    $betrag = $_POST['betrag'];

    // SQL-Query, um den neuen Eintrag hinzuzufügen
    $sql = "INSERT INTO finanzen (typ, kategorie, notiz, betrag, erstellt_von) 
            VALUES (:typ, :kategorie, :notiz, :betrag, :erstellt_von)";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':typ', $typ);
        $stmt->bindParam(':kategorie', $kategorie);
        $stmt->bindParam(':notiz', $notiz);
        $stmt->bindParam(':betrag', $betrag);
        $stmt->bindParam(':erstellt_von', $erstellt_von);

        // Query ausführen
        $stmt->execute();

        // Erfolgreiche Antwort zurückgeben
        echo json_encode(["status" => "success", "message" => "Eintrag erfolgreich hinzugefügt!"]);
    } catch (PDOException $e) {
        // Fehlerbehandlung
        echo json_encode(["status" => "error", "message" => "Fehler: " . $e->getMessage()]);
    }
}
?>
