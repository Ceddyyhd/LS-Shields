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
$ausruestung = $_POST['ausruestung'];  // Die Ausrüstungsänderungen

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

        if ($existing) {
            // Wenn der Status geändert wurde, dann update
            if ($existing['status'] != $status) {
                $stmt = $conn->prepare("UPDATE benutzer_ausruestung SET status = :status WHERE user_id = :user_id AND key_name = :key_name");
                $stmt->execute([':status' => $status, ':user_id' => $user_id, ':key_name' => $key_name]);

                // Füge eine neue Zeile in die History-Tabelle hinzu
                $stmt = $conn->prepare("INSERT INTO ausruestung_history (user_id, key_name, old_status, new_status, changed_by) VALUES (:user_id, :key_name, :old_status, :new_status, :changed_by)");
                $stmt->execute([
                    ':user_id' => $user_id,
                    ':key_name' => $key_name,
                    ':old_status' => $existing['status'],
                    ':new_status' => $status,
                    ':changed_by' => $_SESSION['user_name']
                ]);
            }
        } else {
            // Füge neuen Eintrag in die Tabelle hinzu, wenn noch nicht vorhanden
            $stmt = $conn->prepare("INSERT INTO benutzer_ausruestung (user_id, key_name, status) VALUES (:user_id, :key_name, :status)");
            $stmt->execute([':user_id' => $user_id, ':key_name' => $key_name, ':status' => $status]);

            // Füge auch hier die Änderung in die History-Tabelle hinzu
            $stmt = $conn->prepare("INSERT INTO ausruestung_history (user_id, key_name, old_status, new_status, changed_by) VALUES (:user_id, :key_name, 0, :new_status, :changed_by)");
            $stmt->execute([
                ':user_id' => $user_id,
                ':key_name' => $key_name,
                ':new_status' => $status,
                ':changed_by' => $_SESSION['user_name']
            ]);
        }

        // Aktualisiere den 'stock' in der Tabelle 'ausruestungstypen'
        $stmt = $conn->prepare("SELECT stock FROM ausruestungstypen WHERE key_name = :key_name");
        $stmt->execute([':key_name' => $key_name]);
        $stock = $stmt->fetchColumn();

        // Wenn der Status 1 ist, wird der Bestand reduziert (Gegenstand wurde hinzugefügt), sonst wird er erhöht
        if ($status == 1) {
            $stmt = $conn->prepare("UPDATE ausruestungstypen SET stock = :stock WHERE key_name = :key_name");
            $stmt->execute([':stock' => $stock - 1, ':key_name' => $key_name]);
        } else {
            $stmt = $conn->prepare("UPDATE ausruestungstypen SET stock = :stock WHERE key_name = :key_name");
            $stmt->execute([':stock' => $stock + 1, ':key_name' => $key_name]);
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
