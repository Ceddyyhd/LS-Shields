<?php
include 'db.php'; // Deine PDO-Datenbankverbindung

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Holen der POST-Daten
    $invoiceNumber = $_POST['invoice_number']; // Rechnungsnummer
    $status = $_POST['status']; // 'Bezahlt' oder 'Offen'
    $typ = $_POST['typ']; // 'Einnahme' oder 'Ausgabe'
    $kategorie = $_POST['kategorie'];
    $notiz = $_POST['notiz'];
    $betrag = $_POST['betrag'];
    $erstellt_von = $_POST['erstellt_von']; // Benutzername des angemeldeten Benutzers

    // Fehlerprotokollierung aktivieren
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    try {
        // 1. Aktualisieren des Rechnungsstatus
        $sqlUpdateStatus = "UPDATE invoices SET status = :status WHERE invoice_number = :invoice_number";
        $stmt = $conn->prepare($sqlUpdateStatus);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':invoice_number', $invoiceNumber);
        $stmt->execute();

        // Überprüfen, ob die Statusaktualisierung erfolgreich war
        if ($stmt->rowCount() > 0) {
            // 2. Einfügen der Finanzdaten in die Tabelle 'finanzen'
            if (!is_numeric($betrag)) {
                throw new Exception("Betrag muss eine Zahl sein.");
            }

            // SQL-Abfrage zum Einfügen der Finanzdaten
            $sqlInsertFinanzen = "INSERT INTO finanzen (typ, kategorie, notiz, betrag, erstellt_von) 
                                  VALUES (:typ, :kategorie, :notiz, :betrag, :erstellt_von)";
            $stmtFinanzen = $conn->prepare($sqlInsertFinanzen);
            $stmtFinanzen->bindParam(':typ', $typ);
            $stmtFinanzen->bindParam(':kategorie', $kategorie);
            $stmtFinanzen->bindParam(':notiz', $notiz);
            $stmtFinanzen->bindParam(':betrag', $betrag);
            $stmtFinanzen->bindParam(':erstellt_von', $erstellt_von);
            $stmtFinanzen->execute();

            // Wenn beide Operationen erfolgreich waren
            echo json_encode(['status' => 'success']);
        } else {
            throw new Exception("Fehler: Keine Änderung des Rechnungsstatus vorgenommen.");
        }

    } catch (PDOException $e) {
        // Fehlerbehandlung für PDO-Fehler
        echo json_encode(['status' => 'error', 'message' => 'Fehler bei der Datenbankabfrage: ' . $e->getMessage()]);
    } catch (Exception $e) {
        // Fehlerbehandlung für allgemeine Fehler
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
