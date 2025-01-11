<?php
include 'db.php'; // Deine PDO-Datenbankverbindung
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Überprüfen, ob das CSRF-Token gültig ist
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['status' => 'error', 'message' => 'Ungültiges CSRF-Token']);
        exit;
    }

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
        // Beginne die Transaktion
        $conn->beginTransaction();

        // 1. Aktualisieren des Rechnungsstatus
        $sqlUpdateStatus = "UPDATE invoices SET status = :status WHERE invoice_number = :invoice_number";
        $stmt = $conn->prepare($sqlUpdateStatus);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':invoice_number', $invoiceNumber);
        $stmt->execute();

        // Überprüfen, ob die Statusaktualisierung erfolgreich war
        if ($stmt->rowCount() > 0) {
            // 2. Einfügen der Finanzdaten in die Tabelle 'finanzen'
            if (!is_numeric($betrag) || empty($betrag)) {
                throw new Exception("Betrag muss eine Zahl sein und darf nicht leer sein.");
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

            // Überprüfen, ob das Insert erfolgreich war
            if ($stmtFinanzen->rowCount() > 0) {
                // Commit der Transaktion
                $conn->commit();

                // Log-Eintrag für die Änderungen
                logAction('UPDATE', 'invoices', 'invoice_number: ' . $invoiceNumber . ', status: ' . $status . ', bearbeitet von: ' . $_SESSION['user_id']);
                logAction('INSERT', 'finanzen', 'typ: ' . $typ . ', kategorie: ' . $kategorie . ', betrag: ' . $betrag . ', erstellt_von: ' . $erstellt_von);

                // Wenn beide Operationen erfolgreich waren
                echo json_encode(['status' => 'success', 'message' => 'Rechnung und Finanzdaten erfolgreich aktualisiert!']);
            } else {
                throw new Exception("Fehler beim Einfügen der Finanzdaten.");
            }
        } else {
            throw new Exception("Fehler beim Aktualisieren des Rechnungsstatus.");
        }
    } catch (Exception $e) {
        // Rollback der Transaktion bei Fehler
        $conn->rollBack();
        error_log('Fehler: ' . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Fehler: ' . $e->getMessage()]);
    }
}

// Funktion zum Loggen von Aktionen
function logAction($action, $table, $details) {
    global $conn;

    // SQL-Abfrage zum Einfügen des Log-Eintrags
    $stmt = $conn->prepare("INSERT INTO logs (action, table_name, details, user_id, timestamp) VALUES (:action, :table_name, :details, :user_id, NOW())");
    $stmt->bindParam(':action', $action, PDO::PARAM_STR);
    $stmt->bindParam(':table_name', $table, PDO::PARAM_STR);
    $stmt->bindParam(':details', $details, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
}
?>
