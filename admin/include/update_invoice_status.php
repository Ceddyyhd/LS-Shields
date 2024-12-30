<?php
include 'db.php'; // Deine PDO-Datenbankverbindung

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $invoiceNumber = $_POST['invoice_number'];
    $status = $_POST['status'];  // 'Bezahlt' oder 'Offen'

    try {
        // Fehlerprotokollierung aktivieren
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        // SQL-Abfrage zum Aktualisieren des Rechnungsstatus
        $sql = "UPDATE invoices SET status = :status WHERE invoice_number = :invoice_number";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':invoice_number', $invoiceNumber);

        // Ausführen der SQL-Abfrage
        $stmt->execute();

        // Überprüfen, ob die Abfrage erfolgreich war
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Keine Änderung in der Datenbank vorgenommen.']);
        }
    } catch (PDOException $e) {
        // Fehlerbehandlung und Ausgabe
        echo json_encode(['status' => 'error', 'message' => 'Fehler bei der Datenbankabfrage: ' . $e->getMessage()]);
    }
}
?>
