<?php
include 'include/db.php';
require_once('plugins/tcpdf/tcpdf.php'); // Lade die TCPDF-Bibliothek

// Rechnungsnummer aus der Anfrage holen
$invoice_number = $_GET['invoice_number'] ?? null;

if (!$invoice_number) {
    die("Fehler: Keine Rechnungsnummer übergeben.");
}

// Abfrage für die Rechnung anhand der Rechnungsnummer
$sql_invoice = "SELECT * FROM invoices WHERE invoice_number = :invoice_number";
$stmt_invoice = $conn->prepare($sql_invoice);
$stmt_invoice->execute(['invoice_number' => $invoice_number]);
$invoice = $stmt_invoice->fetch(PDO::FETCH_ASSOC);

if (!$invoice) {
    die("Fehler: Rechnung nicht gefunden.");
}

// Rechnungspositionen dekodieren
$invoice_items = json_decode($invoice['description'], true);
if (!$invoice_items) {
    die("Fehler beim Dekodieren der Rechnungspositionen.");
}

// Kundenabfrage
$sql_customer = "SELECT * FROM kunden WHERE id = :customer_id";
$stmt_customer = $conn->prepare($sql_customer);
$stmt_customer->execute(['customer_id' => $invoice['customer_id']]);
$customer = $stmt_customer->fetch(PDO::FETCH_ASSOC);

// Erstelle eine neue TCPDF-Instanz
$pdf = new TCPDF('P', 'mm', 'A4');
$pdf->SetMargins(10, 10, 10);  // Setze Ränder
$pdf->AddPage();

// Setze Schriftart und Größe
$pdf->SetFont('helvetica', 'B', 16);

// Rechnungsüberschrift
$pdf->Cell(0, 10, 'Rechnung #' . htmlspecialchars($invoice['invoice_number']), 0, 1, 'C');

// Setze eine kleinere Schriftart für den Text
$pdf->SetFont('helvetica', '', 12);

// Kundendaten
$pdf->Ln(5);
$pdf->Cell(0, 10, 'Kunde: ' . htmlspecialchars($customer['name']), 0, 1);
$pdf->Cell(0, 10, 'Email: ' . htmlspecialchars($customer['umail']), 0, 1);

// Rechnungspositionen
$pdf->Ln(5);
$pdf->Cell(0, 10, 'Positionen:', 0, 1);
foreach ($invoice_items as $item) {
    $subtotal = $item['unit_price'] * $item['quantity'];
    $pdf->Cell(0, 10, htmlspecialchars($item['description']) . ' - ' . $item['quantity'] . ' x ' . $item['unit_price'] . '$', 0, 1);
}

// Rabatt und Gesamt
$pdf->Ln(5);
$pdf->Cell(0, 10, 'Rabatt: -' . htmlspecialchars($invoice['discount']) . '$', 0, 1);
$pdf->Cell(0, 10, 'Gesamt: ' . htmlspecialchars($invoice['price']) . '$', 0, 1);

// Rechnungsstatus
$pdf->Ln(5);
$pdf->Cell(0, 10, 'Status: ' . htmlspecialchars($invoice['status']), 0, 1);

// Füge zusätzliche Felder hinzu, falls nötig
// Zum Beispiel ein Footer, oder eine Fußzeile mit Bankdetails oder weiteren Angaben

// Dateiname für das PDF
$pdf_file = $_SERVER['DOCUMENT_ROOT'] . '/admin/invoices/LS-Shields_Rechnung_' . $invoice_number . '.pdf';
$pdf_url = '/admin/invoices/LS-Shields_Rechnung_' . $invoice_number . '.pdf';

// Generiere die PDF und speichere sie auf dem Server
$pdf->Output($pdf_file, 'F');  // Speichern der Datei auf dem Server

// Rückgabe des Pfads zur gespeicherten Datei
echo $pdf_url;
?>
