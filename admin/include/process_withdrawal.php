<?php
include 'security_check.php'; // Sicherheitsprüfung für diese Datei

include 'db.php';

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
        $stmtHistory->execute([
            $withdrawalData['user_id'],
            $withdrawalData['betrag'],
            'Auszahlung',  // Hier wird 'Auszahlung' als Art eingetragen
            "Gehalt: " . $withdrawalData['gehalt'] . ", Anteil: " . $withdrawalData['anteil'] . ", Trinkgeld: " . $withdrawalData['trinkgeld'],
            $erstellt_von
        ]);

        // Eintrag in `finanzen` Tabelle für Ausgaben mit dem Notiztext "Mitarbeiter Auszahlung: Name (Gehalt: 50, Anteil: 0, Trinkgeld: 0)"
        $stmtFinanzen = $conn->prepare("INSERT INTO finanzen (typ, kategorie, notiz, betrag, erstellt_von) VALUES ('Ausgabe', 'Mitarbeiter', ?, ?, ?)");
        $stmtFinanzen->execute([
            "Mitarbeiter Auszahlung: " . $withdrawalData['user_id'] . " (Gehalt: " . $withdrawalData['gehalt'] . ", Anteil: " . $withdrawalData['anteil'] . ", Trinkgeld: " . $withdrawalData['trinkgeld'] . ")",
            $withdrawalData['betrag'],
            $erstellt_von
        ]);

        echo json_encode(['success' => true, 'message' => 'Auszahlung erfolgreich durchgeführt.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Mitarbeiter nicht gefunden.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Fehlende POST-Daten.']);
}
?>
