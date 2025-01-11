<?php
include 'db.php';
session_start();

// Überprüfen, ob das Formular abgeschickt wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Überprüfen, ob das CSRF-Token gültig ist
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header('Location: ../error.php');
        exit;
    }

    // Eingabedaten erhalten
    $customer_id = filter_input(INPUT_POST, 'kunden_id', FILTER_VALIDATE_INT);
    $description = $_POST['beschreibung'] ?? [];
    $unit_price = $_POST['stueckpreis'] ?? [];
    $quantity = $_POST['anzahl'] ?? [];
    $discount = filter_input(INPUT_POST, 'rabatt', FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    if (!$customer_id || empty($description)) {
        echo json_encode(["status" => "error", "message" => "Fehler: Kunden-ID oder Rechnungsdaten fehlen."]);
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
    $end_discount = $total_price * ($discount / 100);
    $total_price = $total_price - $end_discount;

    try {
        // SQL-Abfrage, um die Rechnung hinzuzufügen
        $stmt = $conn->prepare("INSERT INTO invoices (customer_id, invoice_number, total_price, discount) VALUES (:customer_id, :invoice_number, :total_price, :discount)");
        $stmt->execute([
            ':customer_id' => $customer_id,
            ':invoice_number' => $invoice_number,
            ':total_price' => $total_price,
            ':discount' => $discount
        ]);

        // Letzte eingefügte Rechnungs-ID abrufen
        $invoice_id = $conn->lastInsertId();

        // Rechnungspositionen hinzufügen
        $stmt = $conn->prepare("INSERT INTO invoice_items (invoice_id, description, unit_price, quantity) VALUES (:invoice_id, :description, :unit_price, :quantity)");
        foreach ($invoice_items as $item) {
            $stmt->execute([
                ':invoice_id' => $invoice_id,
                ':description' => $item['description'],
                ':unit_price' => $item['unit_price'],
                ':quantity' => $item['quantity']
            ]);
        }

        // Log-Eintrag für das Erstellen der Rechnung
        logAction('INSERT', 'invoices', 'invoice_id: ' . $invoice_id . ', created_by: ' . $_SESSION['user_id']);

        echo json_encode(['status' => 'success', 'message' => 'Rechnung erfolgreich erstellt.']);
    } catch (PDOException $e) {
        error_log('Fehler beim Erstellen der Rechnung: ' . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Fehler beim Erstellen der Rechnung: ' . $e->getMessage()]);
    }
} else {
    header('Location: ../error.php');
    exit;
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
