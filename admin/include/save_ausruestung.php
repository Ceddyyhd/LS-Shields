<?php
// Fehlerprotokollierung aktivieren (für Debugging)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Setze Content-Type auf JSON
header('Content-Type: application/json');

// Datenbankverbindung einbinden
include 'db.php';

// Empfangen der Formulardaten via POST
$user_id = $_POST['user_id'];  // Benutzer-ID
$user_name = $_POST['user_name'];  // Benutzername aus dem Formular

// Überprüfe, ob Ausrüstungsdaten gesendet wurden
if (isset($_POST['ausruestung']) && is_array($_POST['ausruestung'])) {
    $ausruestung = $_POST['ausruestung'];  // Die Ausrüstungsänderungen
} else {
    // Wenn keine Checkbox aktiviert wurde, setzen wir alle Artikel auf "zurückgegeben"
    $ausruestung = [];
    $stmt = $conn->prepare("SELECT key_name FROM ausruestungstypen");
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Setze den Status aller Artikel auf "zurückgegeben" (d.h., 0)
    foreach ($items as $item) {
        $ausruestung[$item['key_name']] = 0; // zurückgegeben
    }
}

// Berechtigungsprüfung (z.B. ob der User die Berechtigung hat, Änderungen vorzunehmen)
session_start();
$canEdit = $_SESSION['permissions']['edit_employee'] ?? false;

if (!$canEdit) {
    echo json_encode(['success' => false, 'message' => 'Keine Berechtigung zum Bearbeiten.']);
    exit;
}

try {
    // Beginne die Transaktion (damit alle Änderungen als eine Einheit gespeichert werden)
    $conn->beginTransaction();

    // Schleife durch die Änderungen und speichere sie in der 'benutzer_ausruestung'-Tabelle
    foreach ($ausruestung as $key_name => $status) {
        // Überprüfe, ob der Benutzer bereits diesen Gegenstand in der Tabelle 'benutzer_ausruestung' hat
        $stmt = $conn->prepare("SELECT status FROM benutzer_ausruestung WHERE user_id = :user_id AND key_name = :key_name");
        $stmt->execute([':user_id' => $user_id, ':key_name' => $key_name]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        // Prüfe den aktuellen Bestand
        $stmt = $conn->prepare("SELECT stock FROM ausruestungstypen WHERE key_name = :key_name");
        $stmt->execute([':key_name' => $key_name]);
        $stock = $stmt->fetchColumn();

        // Wenn der Status auf 1 (ausgegeben) geändert wird, überprüfen, ob genug Bestand vorhanden ist
        if ($status == 1 && $stock <= 0) {
            echo json_encode(['success' => false, 'message' => 'Nicht genügend Artikel auf Lager!']);
            exit;
        }

        if ($existing) {
            // Wenn der Status geändert wurde, dann update
            if ($existing['status'] != $status) {
                // Status in der Tabelle benutzer_ausruestung aktualisieren
                $stmt = $conn->prepare("UPDATE benutzer_ausruestung SET status = :status WHERE user_id = :user_id AND key_name = :key_name");
                $stmt->execute([':status' => $status, ':user_id' => $user_id, ':key_name' => $key_name]);

                // Bestimme die Aktion (Hinzufügung oder Zurückgabe)
                $action = ($status == 1) ? 'Hinzufügung' : 'Zurückgabe';
                $stock_change = ($status == 1) ? -1 : 1; // Bestand ändern: -1, wenn entfernt, +1, wenn hinzugefügt

                // Füge eine neue Zeile in die History-Tabelle hinzu
                $stmt = $conn->prepare("INSERT INTO ausruestung_history (user_id, key_name, action, stock_change, editor_name) VALUES (:user_id, :key_name, :action, :stock_change, :editor_name)");
                $stmt->execute([
                    ':user_id' => $user_id,
                    ':key_name' => $key_name,
                    ':action' => $action,
                    ':stock_change' => $stock_change,
                    ':editor_name' => $user_name
                ]);
            }
        } else {
            // Füge neuen Eintrag in die Tabelle hinzu, wenn noch nicht vorhanden
            $stmt = $conn->prepare("INSERT INTO benutzer_ausruestung (user_id, key_name, status) VALUES (:user_id, :key_name, :status)");
            $stmt->execute([':user_id' => $user_id, ':key_name' => $key_name, ':status' => $status]);

            // Bestimme die Aktion (Hinzufügung)
            $action = 'Hinzufügung';
            $stock_change = -1; // Bestand reduzieren, weil ein neues Item hinzugefügt wird

            // Füge auch hier die Änderung in die History-Tabelle hinzu
            $stmt = $conn->prepare("INSERT INTO ausruestung_history (user_id, key_name, action, stock_change, editor_name) VALUES (:user_id, :key_name, :action, :stock_change, :editor_name)");
            $stmt->execute([
                ':user_id' => $user_id,
                ':key_name' => $key_name,
                ':action' => $action,
                ':stock_change' => $stock_change,
                ':editor_name' => $user_name
            ]);
        }

        // Wenn der Artikel hinzugefügt wurde, reduziere den Bestand
        if ($status == 1 && $existing['status'] == 0) {
            $stmt = $conn->prepare("UPDATE ausruestungstypen SET stock = :stock WHERE key_name = :key_name");
            $stmt->execute([':stock' => $stock - 1, ':key_name' => $key_name]);
        }

        // Wenn der Artikel zurückgegeben wurde, erhöhe den Bestand und logge in History
        if ($status == 0 && $existing['status'] == 1) {
            $stmt = $conn->prepare("UPDATE ausruestungstypen SET stock = :stock WHERE key_name = :key_name");
            $stmt->execute([':stock' => $stock + 1, ':key_name' => $key_name]);

            // History-Eintrag für zurückgegebene Ausrüstung
            $stmt = $conn->prepare("INSERT INTO ausruestung_history (user_id, key_name, action, stock_change, editor_name) VALUES (:user_id, :key_name, 'Zurückgabe', 1, :editor_name)");
            $stmt->execute([
                ':user_id' => $user_id,
                ':key_name' => $key_name,
                ':editor_name' => $user_name
            ]);
        }
    }

    // Alle Änderungen als Transaktion abschließen
    $conn->commit();

    // Erfolgreiche Antwort zurückgeben
    echo json_encode(['success' => true, 'message' => 'Änderungen wurden erfolgreich gespeichert!']);
} catch (Exception $e) {
    // Bei Fehlern wird die Transaktion zurückgerollt
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
}
