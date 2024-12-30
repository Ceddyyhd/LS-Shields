<?php
require_once('tcpdf_include.php');  // TCPDF Bibliothek einbinden
include 'include/db.php';  // Datenbankverbindung einbinden

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

// Erweiterte TCPDF-Klasse für benutzerdefinierte Header und Footer
class MYPDF extends TCPDF {

    // Header
    public function Header() {
        $this->SetFont('helvetica', 'B', 16);
        $this->Cell(0, 15, 'Rechnung #' . htmlspecialchars($invoice['invoice_number']), 0, 1, 'C');
        $this->SetFont('helvetica', '', 12);
        $this->Cell(0, 10, 'Ausgestellt am: ' . htmlspecialchars($invoice['created_at']), 0, 1, 'C');
    }

    // Footer
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Seite ' . $this->getAliasNumPage() . ' von ' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

// TCPDF-Dokument erstellen
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Setze Schriftarten und Dokumentinformationen
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('LS Shields');
$pdf->SetTitle('Rechnung ' . htmlspecialchars($invoice['invoice_number']));
$pdf->SetSubject('Rechnung PDF');

// Setze Header und Footer
$pdf->SetHeaderData('', 0, 'LS Shields', 'Rechnung');

// Setze die Ränder
$pdf->SetMargins(10, 10, 10);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(5);

// Fügt eine Seite hinzu
$pdf->AddPage();

// Rechnungsinformationen ausgeben
$pdf->SetFont('helvetica', '', 12);
$pdf->Ln(10);

// Kundeninformationen
$pdf->Cell(0, 10, 'Kunde: ' . htmlspecialchars($customer['name']), 0, 1);
$pdf->Cell(0, 10, 'Email: ' . htmlspecialchars($customer['umail']), 0, 1);
$pdf->Cell(0, 10, 'Telefon: ' . htmlspecialchars($customer['nummer']), 0, 1);

// Rechnungspositionen
$pdf->Ln(5);
$pdf->Cell(0, 10, 'Positionen:', 0, 1);

$header = array('Beschreibung', 'Einzelpreis', 'Menge', 'Gesamt');
$data = [];

foreach ($invoice_items as $item) {
    $data[] = array(
        'description' => htmlspecialchars($item['description']),
        'unit_price' => $item['unit_price'] . ' $',
        'quantity' => $item['quantity'],
        'total' => ($item['unit_price'] * $item['quantity']) . ' $'
    );
}

// Ausgabe der Tabelle
$pdf->SetFont('helvetica', '', 10);
$pdf->ColoredTable($header, $data);

// Rabatt und Gesamt berechnen
$pdf->Ln(5);
$pdf->Cell(0, 10, 'Rabatt: -' . htmlspecialchars($invoice['discount']) . ' $', 0, 1);
$pdf->Cell(0, 10, 'Gesamt: ' . htmlspecialchars($invoice['price']) . ' $', 0, 1);

// Rechnungsstatus ausgeben
$pdf->Ln(5);
$pdf->Cell(0, 10, 'Status: ' . htmlspecialchars($invoice['status']), 0, 1);

// PDF speichern und ausgeben
$pdf_file = $_SERVER['DOCUMENT_ROOT'] . '/admin/invoices/LS-Shields_Rechnung_' . $invoice_number . '.pdf';
$pdf_url = '/admin/invoices/LS-Shields_Rechnung_' . $invoice_number . '.pdf';

// PDF generieren und speichern
$pdf->Output($pdf_file, 'F');

// Gebe die URL des gespeicherten PDFs zurück
echo $pdf_url;
?>
