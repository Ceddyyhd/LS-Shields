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
        die("Fehler: Kunden-ID oder Rechnungsdaten fehlen.");
    }

    // Zufällige 5-stellige Rechnungsnummer generieren
    $invoice_number = rand(10000, 99999);

    // Gesamtpreis berechnen
    $total_price = 0;
    foreach ($description as $index => $desc) {
        // Umwandlung der Werte in numerische Werte (float für Preis und int für Menge)
        $price = (float)$unit_price[$index];  // Stückpreis als float
        $qty = (int)$quantity[$index];         // Anzahl als int
        
        // Berechnung des Gesamtpreises der einzelnen Position
        $total_price += ($price * $qty);
    }

    // Rabatt anwenden
    $total_price = $total_price - ($total_price * ($discount / 100));

    // Rechnungsdaten in der Datenbank speichern
    $sql = "INSERT INTO invoices (customer_id, invoice_number, description, price, discount, created_at) 
            VALUES (:customer_id, :invoice_number, :description, :price, :discount, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'customer_id' => $customer_id,
        'invoice_number' => $invoice_number,
        'description' => json_encode(array_map(function($desc, $price, $qty) {
            return ['description' => $desc, 'unit_price' => $price, 'quantity' => $qty];
        }, $description, $unit_price, $quantity)),
        'price' => $total_price,
        'discount' => $discount
    ]);

    // Weiterleitung zur Rechnungsansicht oder zur Bestätigungsseite
    echo "Rechnung erfolgreich erstellt. Rechnungsnummer: " . $invoice_number;
}
?>
