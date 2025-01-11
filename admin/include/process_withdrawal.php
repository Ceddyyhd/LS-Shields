<?php
include 'db.php';
session_start();
header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Ungültiges CSRF-Token']);
    exit;
}

// Überprüfen, ob die Daten gesendet wurden
if (isset($_POST['withdrawalData'])) {
    // Dekodieren der JSON-Daten
    $withdrawalData = json_decode($_POST['withdrawalData'], true);

    // Überprüfen, ob alle erforderlichen Felder vorhanden sind
    if (!isset($withdrawalData['user_id'], $withdrawalData['gehalt'], $withdrawalData['anteil'], $withdrawalData['trinkgeld'], $withdrawalData['betrag'], $withdrawalData['erstellt_von'])) {
        echo json_encode(['success' => false, 'message' => 'Fehlende Daten.']);
        exit;
    }

    // Hole den Benutzer, der den Eintrag erstellt hat
    $erstellt_von = $withdrawalData['erstellt_von'];

    // Hole den aktuellen Stand des Mitarbeiters
    $stmtTotal = $conn->prepare("SELECT * FROM mitarbeiter_finanzen WHERE user_id = ?");
    $stmtTotal->execute([$withdrawalData['user_id']]);
    $total = $stmtTotal->fetch();

    if ($total) {
        // Überprüfen, ob der angegebene Betrag nicht höher als das Gesamtguthaben (Gehalt, Anteil, Trinkgeld) ist
        $gesamtBetrag = $total['gehalt'] + $total['anteil'] + $total['trinkgeld'];
        if ($withdrawalData['betrag'] > $gesamtBetrag) {
            echo json_encode(['success' => false, 'message' => 'Der angegebene Betrag ist größer als das verfügbare Gesamtguthaben.']);
            exit;
        }

        // Überprüfen, ob der angegebene Betrag für Gehalt, Anteil und Trinkgeld nicht höher ist als das verfügbare Guthaben
        if ($withdrawalData['gehalt'] > $total['gehalt'] || $withdrawalData['anteil'] > $total['anteil'] || $withdrawalData['trinkgeld'] > $total['trinkgeld']) {
            echo json_encode(['success' => false, 'message' => 'Der angegebene Betrag für Gehalt, Anteil oder Trinkgeld ist größer als das verfügbare Guthaben.']);
            exit;
        }

        // Berechnen und abziehen der Beträge aus `mitarbeiter_finanzen`
        $newGehalt = $total['gehalt'] - $withdrawalData['gehalt'];
        $newAnteil = $total['anteil'] - $withdrawalData['anteil'];
        $newTrinkgeld = $total['trinkgeld'] - $withdrawalData['trinkgeld'];

        // Update der `mitarbeiter_finanzen` Tabelle
        $stmtUpdate = $conn->prepare("UPDATE mitarbeiter_finanzen SET gehalt = ?, anteil = ?, trinkgeld = ? WHERE user_id = ?");
        $stmtUpdate->execute([$newGehalt, $newAnteil, $newTrinkgeld, $withdrawalData['user_id']]);

        // Eintrag in `finanzen_history` (historische Transaktionen) mit 'Auszahlung' als Art
        $stmtHistory = $conn->prepare("INSERT INTO finanzen_history (user_id, betrag, art, notiz, erstellt_von) VALUES (?, ?, ?, ?, ?)");
        $stmtHistory->execute([$withdrawalData['user_id'], $withdrawalData['betrag'], 'Auszahlung', $withdrawalData['notiz'], $erstellt_von]);

        // Log-Eintrag für die Auszahlung
        logAction('WITHDRAWAL', 'mitarbeiter_finanzen', 'user_id: ' . $withdrawalData['user_id'] . ', betrag: ' . $withdrawalData['betrag'] . ', erstellt_von: ' . $erstellt_von);

        echo json_encode(['success' => true, 'message' => 'Auszahlung erfolgreich verarbeitet.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Mitarbeiter nicht gefunden.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Keine Daten gesendet.']);
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
