<?php
include 'db.php'; // Deine PDO-Datenbankverbindung

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $typ = $_POST['typ'];  // 'Einnahme' oder 'Ausgabe'
    $kategorie = $_POST['kategorie'];
    $notiz = $_POST['notiz'];
    $betrag = $_POST['betrag'];
    $erstellt_von = $_POST['erstellt_von'];  // Benutzername des angemeldeten Benutzers

    // Debugging: Ausgabe der übergebenen Werte
    error_log("Daten empfangen: Typ = $typ, Kategorie = $kategorie, Notiz = $notiz, Betrag = $betrag, Erstellt von = $erstellt_von");

    // Überprüfen, ob Betrag eine Zahl ist
    if (!is_numeric($betrag)) {
        error_log("Fehler: Betrag muss eine Zahl sein.");
        echo json_encode(['status' => 'error', 'message' => 'Betrag muss eine Zahl sein.']);
        exit(); // Beende das Skript, falls der Betrag ungültig ist
    }

    try {
        // SQL-Abfrage zum Einfügen der Finanzdaten in die Tabelle
        $sql = "INSERT INTO finanzen (typ, kategorie, notiz, betrag, erstellt_von) 
                VALUES (:typ, :kategorie, :notiz, :betrag, :erstellt_von)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':typ', $typ);
        $stmt->bindParam(':kategorie', $kategorie);
        $stmt->bindParam(':notiz', $notiz);
        $stmt->bindParam(':betrag', $betrag);
        $stmt->bindParam(':erstellt_von', $erstellt_von);

        // Fehlerprotokollierung: Vor dem Ausführen der Anfrage
        error_log("SQL-Befehl: " . $sql);

        // SQL-Ausführung
        $stmt->execute();

        // Erfolg zurückgeben
        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        // Fehlerprotokollierung für PDO-Fehler
        error_log("PDO Fehler: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => "Fehler bei der Datenbankabfrage: " . $e->getMessage()]);
    } catch (Exception $e) {
        // Fehlerprotokollierung für allgemeine Fehler
        error_log("Fehler: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
