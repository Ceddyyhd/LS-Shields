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
        if ($status == 1) {
            if ($stock <= 0) {
                echo json_encode(['success' => false, 'message' => 'Nicht genügend Artikel auf Lager!']);
                exit;
            }
        }

        // Wenn der Artikel zurückgegeben wird (Status 0)
        if ($status == 0) {
            // Wenn der Artikel bereits ausgegeben war, dann zurückgeben
            if ($existing && $existing['status'] == 1) {
                // Bestands-Update und History-Eintrag, wenn Artikel zurückgegeben wird
                $stmt = $conn->prepare("UPDATE benutzer_ausruestung SET status = 0 WHERE user_id = :user_id AND key_name = :key_name");
                $stmt->execute([':user_id' => $user_id, ':key_name' => $key_name]);

                // Bestands-Update in der Tabelle ausruestungstypen (Stock erhöhen)
                $stmt = $conn->prepare("UPDATE ausruestungstypen SET stock = stock + 1 WHERE key_name = :key_name");
                $stmt->execute([':key_name' => $key_name]);

                // History-Eintrag
                $stmt = $conn->prepare("INSERT INTO ausruestung_history (user_id, key_name, action, stock_change, editor_name) VALUES (:user_id, :key_name, 'Zurückgabe', 1, :editor_name)");
                $stmt->execute([
                    ':user_id' => $user_id,
                    ':key_name' => $key_name,
                    ':editor_name' => $user_name
                ]);
            }
        } elseif ($status == 1) {
            // Wenn der Artikel ausgegeben wird (Status 1)
            if ($existing) {
                // Wenn der Artikel bereits vorhanden war, update den Status nur, wenn der Status 0 war
                if ($existing['status'] == 0) {
                    // Bestands-Update in der Tabelle ausruestungstypen (Stock verringern)
                    $stmt = $conn->prepare("UPDATE ausruestungstypen SET stock = stock - 1 WHERE key_name = :key_name");
                    $stmt->execute([':key_name' => $key_name]);

                    // Bestandsänderung in der benutzer_ausruestung
                    $stmt = $conn->prepare("UPDATE benutzer_ausruestung SET status = 1 WHERE user_id = :user_id AND key_name = :key_name");
                    $stmt->execute([':user_id' => $user_id, ':key_name' => $key_name]);

                    // History-Eintrag
                    $stmt = $conn->prepare("INSERT INTO ausruestung_history (user_id, key_name, action, stock_change, editor_name) VALUES (:user_id, :key_name, 'Hinzufügung', -1, :editor_name)");
                    $stmt->execute([ 
                        ':user_id' => $user_id, 
                        ':key_name' => $key_name, 
                        ':editor_name' => $user_name 
                    ]);
                }
            } else {
                // Artikel ist noch nicht vergeben, also setze ihn als ausgegeben
                $stmt = $conn->prepare("INSERT INTO benutzer_ausruestung (user_id, key_name, status) VALUES (:user_id, :key_name, 1)");
                $stmt->execute([':user_id' => $user_id, ':key_name' => $key_name]);

                // Bestands-Update in der Tabelle ausruestungstypen (Stock verringern)
                $stmt = $conn->prepare("UPDATE ausruestungstypen SET stock = stock - 1 WHERE key_name = :key_name");
                $stmt->execute([':key_name' => $key_name]);

                // History-Eintrag
                $stmt = $conn->prepare("INSERT INTO ausruestung_history (user_id, key_name, action, stock_change, editor_name) VALUES (:user_id, :key_name, 'Hinzufügung', -1, :editor_name)");
                $stmt->execute([ 
                    ':user_id' => $user_id, 
                    ':key_name' => $key_name, 
                    ':editor_name' => $user_name 
                ]);
            }
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
