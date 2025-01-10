<?php
include 'db.php';

// Überprüfen, ob die Daten gesendet wurden
if (isset($_POST['historyData']) && isset($_POST['totalData'])) {
    // Dekodieren der JSON-Daten
    $historyData = json_decode($_POST['historyData'], true);
    $totalData = json_decode($_POST['totalData'], true);

    // Überprüfen, ob die benötigten Daten vorhanden sind
    if (!isset($historyData['user_id'], $historyData['betrag'], $historyData['art'], $historyData['notiz']) ||
        !isset($totalData['user_id'], $totalData['art'], $totalData['betrag'])) {
        echo json_encode(['success' => false, 'message' => 'Fehlende Daten.']);
        exit;
    }

    // Hole den Benutzer, der den Eintrag erstellt hat
    $erstellt_von = $_SESSION['username']; // Der Benutzername aus der Session

    // Daten für Historie speichern
    $stmt = $conn->prepare("INSERT INTO finanzen_history (user_id, betrag, art, notiz, erstellt_von) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$historyData['user_id'], $historyData['betrag'], $historyData['art'], $historyData['notiz'], $erstellt_von]);

    // Gesamtbetrag aktualisieren
    $stmtTotal = $conn->prepare("SELECT * FROM mitarbeiter_finanzen WHERE user_id = ?");
    $stmtTotal->execute([$totalData['user_id']]);
    $total = $stmtTotal->fetch();

    if ($total) {
        // Mitarbeiter existiert, Gesamtbetrag aktualisieren
        if ($totalData['art'] == 'Gehalt') {
            $newAmount = $total['gehalt'] + $totalData['betrag'];
            $stmtUpdate = $conn->prepare("UPDATE mitarbeiter_finanzen SET gehalt = ? WHERE user_id = ?");
            $stmtUpdate->execute([$newAmount, $totalData['user_id']]);
        } elseif ($totalData['art'] == 'Anteil') {
            $newAmount = $total['anteil'] + $totalData['betrag'];
            $stmtUpdate = $conn->prepare("UPDATE mitarbeiter_finanzen SET anteil = ? WHERE user_id = ?");
            $stmtUpdate->execute([$newAmount, $totalData['user_id']]);
        } elseif ($totalData['art'] == 'Trinkgeld') {
            $newAmount = $total['trinkgeld'] + $totalData['betrag'];
            $stmtUpdate = $conn->prepare("UPDATE mitarbeiter_finanzen SET trinkgeld = ? WHERE user_id = ?");
            $stmtUpdate->execute([$newAmount, $totalData['user_id']]);
        }
    } else {
        // Mitarbeiter hat noch keinen Eintrag, also einen neuen Datensatz erstellen
        $stmtInsert = $conn->prepare("INSERT INTO mitarbeiter_finanzen (user_id, gehalt, anteil, trinkgeld) VALUES (?, ?, ?, ?)");
        $stmtInsert->execute([$totalData['user_id'], 
                              ($totalData['art'] == 'Gehalt') ? $totalData['betrag'] : 0,
                              ($totalData['art'] == 'Anteil') ? $totalData['betrag'] : 0,
                              ($totalData['art'] == 'Trinkgeld') ? $totalData['betrag'] : 0]);
    }

    echo json_encode(['success' => true, 'message' => 'Daten wurden erfolgreich gespeichert.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Fehlende POST-Daten.']);
}
?>
