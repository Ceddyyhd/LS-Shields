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
if (isset($_POST['historyData']) && isset($_POST['totalData'])) {
    // Dekodieren der JSON-Daten
    $historyData = json_decode($_POST['historyData'], true);
    $totalData = json_decode($_POST['totalData'], true);

    // Überprüfen, ob die benötigten Daten vorhanden sind
    if (!isset($historyData['user_id'], $historyData['betrag'], $historyData['art'], $historyData['notiz'], $historyData['erstellt_von']) ||
        !isset($totalData['user_id'], $totalData['art'], $totalData['betrag'])) {
        echo json_encode(['success' => false, 'message' => 'Fehlende Daten.']);
        exit;
    }

    // Hole den Benutzer, der den Eintrag erstellt hat (aus den übergebenen Daten)
    $erstellt_von = $historyData['erstellt_von']; // Der Benutzername wird nun übergeben

    try {
        // Beginne die Transaktion
        $conn->beginTransaction();

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

        // Commit der Transaktion
        $conn->commit();

        // Log-Eintrag für die Änderungen
        logAction('INSERT', 'finanzen_history', 'user_id: ' . $historyData['user_id'] . ', betrag: ' . $historyData['betrag'] . ', art: ' . $historyData['art'] . ', erstellt_von: ' . $erstellt_von);

        echo json_encode(['success' => true, 'message' => 'Eintrag erfolgreich gespeichert.']);
    } catch (PDOException $e) {
        // Rollback der Transaktion bei Fehler
        $conn->rollBack();
        error_log('Fehler beim Speichern des Eintrags: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern des Eintrags: ' . $e->getMessage()]);
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
