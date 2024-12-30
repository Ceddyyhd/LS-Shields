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

    try {
        // Überprüfen, ob Betrag eine Zahl ist
        if (!is_numeric($betrag)) {
            throw new Exception("Betrag muss eine Zahl sein.");
        }

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
    } catch (Exception $e) {
        // Fehlerbehandlung
        error_log("Fehler: " . $e->getMessage()); // Fehler ins Log schreiben
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    } catch (PDOException $e) {
        // Fehlerbehandlung für PDO Fehler
        error_log("PDO Fehler: " . $e->getMessage()); // Fehler ins Log schreiben
        echo json_encode(['status' => 'error', 'message' => "Fehler bei der Datenbankabfrage: " . $e->getMessage()]);
    }
}
?>
