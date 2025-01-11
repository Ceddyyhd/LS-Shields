<?php
include 'db.php';  // Datenbankverbindung einbinden
session_start();   // Session starten, um auf $_SESSION zuzugreifen

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Überprüfen, ob das CSRF-Token gültig ist
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false, 'message' => 'Ungültiges CSRF-Token']);
        exit;
    }

    // Werte aus dem POST holen
    $vehicle_id = $_POST['vehicle_id'];
    $license_plate = $_POST['license_plate'];  // Kennzeichen
    $fuel_checked = isset($_POST['fuel_checked']) ? 1 : 0;  // Checkbox "Getankt"
    $fuel_location = isset($_POST['fuel_location']) ? $_POST['fuel_location'] : NULL;  // Textfeld "Wo?"
    $fuel_amount = isset($_POST['fuel_amount']) ? $_POST['fuel_amount'] : NULL;  // Betrag
    $user_name = $_SESSION['username'];  // Benutzername aus der Session holen

    try {
        // Fahrzeugdaten abrufen
        $sql_select = "SELECT * FROM vehicles WHERE id = ?";
        $stmt_select = $conn->prepare($sql_select);
        $stmt_select->execute([$vehicle_id]);
        $old_vehicle = $stmt_select->fetch(PDO::FETCH_ASSOC);

        if (!$old_vehicle) {
            echo json_encode(['success' => false, 'message' => 'Fahrzeug nicht gefunden.']);
            exit;
        }

        // Log-Eintrag für Fahrzeugänderung (nur die Änderung der Tanken-Daten)
        $changes = [];
        if ($fuel_checked) {
            $changes[] = "Getankt: Ja, Deckel: Ja";
        } else {
            $changes[] = "Getankt: Ja, Deckel: Nein";
        }

        if ($fuel_location !== $old_vehicle['fuel_location']) {
            $changes[] = "Wo getankt: " . $old_vehicle['fuel_location'] . " -> " . $fuel_location;
        }

        // Betrag einfügen, wenn verfügbar
        if ($fuel_amount !== NULL) {
            $changes[] = "Betrag -> " . $fuel_amount . " $";
        }

        // Wenn Änderungen in den Tanken-Daten vorliegen, Eintrag ins Log
        if (!empty($changes)) {
            $action = "Fahrzeug Tanken ($license_plate) (" . implode(", ", $changes) . ")";  // Hier wird das Kennzeichen und der Betrag korrekt gesetzt
            $log_sql = "INSERT INTO vehicles_logs (vehicle_id, action, user_name) VALUES (?, ?, ?)";
            $log_stmt = $conn->prepare($log_sql);
            $log_stmt->execute([$vehicle_id, $action, $user_name]);

            // Allgemeiner Log-Eintrag
            logAction('UPDATE', 'vehicles', 'Fahrzeug Tanken: ID: ' . $vehicle_id . ', geändert von: ' . $_SESSION['user_id']);
        }

        // Fahrzeugdaten aktualisieren
        $sql_update = "UPDATE vehicles SET fuel_checked = ?, fuel_location = ?, fuel_amount = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->execute([$fuel_checked, $fuel_location, $fuel_amount, $vehicle_id]);

        echo json_encode(['success' => true, 'message' => 'Fahrzeugdaten erfolgreich aktualisiert.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Aktualisieren der Fahrzeugdaten: ' . $e->getMessage()]);
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
