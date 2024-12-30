<?php
include 'db.php';
session_start();

// Überprüfen, ob das Formular abgeschickt wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Eingabedaten erhalten
    $customer_id = $_POST['kunden_id'] ?? null;
    $description = $_POST['beschreibung'] ?? [];
    $unit_price = $_POST['stueckpreis'] ?? [];
    $quantity = $_POST['anzahl'] ?? [];
    $discount = $_POST['rabatt'] ?? 0;

    if (!$customer_id || empty($description)) {
        echo "Fehler: Kunden-ID oder Rechnungsdaten fehlen.";
        exit;
    }

    // Zufällige 5-stellige Rechnungsnummer generieren
    $invoice_number = rand(10000, 99999);

    // Gesamtpreis berechnen
    $total_price = 0;
    $invoice_items = [];
    
    foreach ($description as $index => $desc) {
        $price = (float)$unit_price[$index];  // Stückpreis als float
        $qty = (int)$quantity[$index];         // Anzahl als int

        // Überspringe leere Positionen
        if (empty($desc) || $price <= 0 || $qty <= 0) {
            continue;  // Diese Position überspringen, wenn sie ungültig ist
        }

        // Berechnung des Gesamtpreises der einzelnen Position
        $total_price += ($price * $qty);

        // Füge Rechnungspositionen zu einem Array hinzu
        $invoice_items[] = [
            'description' => $desc,
            'unit_price' => $price,
            'quantity' => $qty
        ];
    }

    // Rabatt anwenden
    $total_price = $total_price - ($total_price * ($discount / 100));

    // Rechnungsdaten in der Datenbank speichern
    if (!empty($invoice_items)) {
        $sql = "INSERT INTO invoices (customer_id, invoice_number, description, price, discount, created_at) 
                VALUES (:customer_id, :invoice_number, :description, :price, :discount, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'customer_id' => $customer_id,
            'invoice_number' => $invoice_number,
            'description' => json_encode($invoice_items),  // JSON für alle Positionen
            'price' => $total_price,
            'discount' => $discount
        ]);

        // Erfolgreiche Antwort zurückgeben
        echo "Rechnung erfolgreich erstellt. Rechnungsnummer: " . $invoice_number;
    } else {
        echo "Fehler: Keine gültigen Rechnungspositionen.";
    }
}
?>
