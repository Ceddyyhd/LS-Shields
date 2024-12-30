<?php
include 'db.php'; // Deine PDO-Datenbankverbindung

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $invoiceNumber = $_POST['invoice_number'];
    $status = $_POST['status'];  // 'Bezahlt' oder 'Offen'

    try {
        // Update der Rechnung in der Tabelle
        $sql = "UPDATE invoices SET status = :status WHERE invoice_number = :invoice_number";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':invoice_number', $invoiceNumber);
        $stmt->execute();

        // Erfolg zurückgeben
        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        // Fehlerbehandlung
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
