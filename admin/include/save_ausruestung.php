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

// Letzte Spind Kontrolle und Notizen
$letzte_spind_kontrolle = $_POST['letzte_spind_kontrolle'] ?? null;
$notizen = $_POST['notizen'] ?? null;

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

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Ungültiges CSRF-Token']);
    exit;
}

try {
    // Beginne die Transaktion (damit alle Änderungen als eine Einheit gespeichert werden)
    $conn->beginTransaction();

    // 1. Letzte Spind Kontrolle und Notizen speichern oder aktualisieren
if ($letzte_spind_kontrolle !== null || $notizen !== null) {
    // Prüfe, ob bereits ein Eintrag für die User_ID existiert
    $stmt = $conn->prepare("SELECT id FROM spind_kontrolle_notizen WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $existingSpindKontrolle = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingSpindKontrolle) {
        // Wenn bereits ein Eintrag existiert, führe ein Update durch
        $stmt = $conn->prepare("
            UPDATE spind_kontrolle_notizen 
            SET letzte_spind_kontrolle = :letzte_spind_kontrolle, notizen = :notizen 
            WHERE user_id = :user_id
        ");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':letzte_spind_kontrolle', $letzte_spind_kontrolle);
        $stmt->bindParam(':notizen', $notizen);
        $stmt->execute();

        // Eintrag in spind_kontrolle_logs (History)
        $stmt = $conn->prepare("
            INSERT INTO spind_kontrolle_logs (user_id, editor_name, action)
            VALUES (:user_id, :editor_name, :action)
        ");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':editor_name', $user_name);
        $stmt->bindParam(':action', $action);
        $action = 'Spind Kontrolle bearbeitet'; // Logeintrag für Bearbeitung
        $stmt->execute();
    } else {
        // Falls noch kein Eintrag existiert, füge einen neuen Eintrag hinzu
        $stmt = $conn->prepare("
            INSERT INTO spind_kontrolle_notizen (user_id, letzte_spind_kontrolle, notizen)
            VALUES (:user_id, :letzte_spind_kontrolle, :notizen)
        ");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':letzte_spind_kontrolle', $letzte_spind_kontrolle);
        $stmt->bindParam(':notizen', $notizen);
        $stmt->execute();

        // Eintrag in spind_kontrolle_logs (History)
        $stmt = $conn->prepare("
            INSERT INTO spind_kontrolle_logs (user_id, editor_name, action)
            VALUES (:user_id, :editor_name, :action)
        ");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':editor_name', $user_name);
        $stmt->bindParam(':action', $action);
        $action = 'Spind Kontrolle hinzugefügt'; // Logeintrag für Hinzufügung
        $stmt->execute();
    }
}


    // 2. Bestandsprüfung und Ausrüstungsänderungen
    foreach ($ausruestung as $key_name => $status) {
        if ($status == 1) { // Nur Artikel mit status = 1 (ausgegeben) prüfen
            // Prüfe, ob der Artikel bereits in benutzer_ausruestung existiert und den status = 1 hat
            $stmt = $conn->prepare("SELECT status FROM benutzer_ausruestung WHERE user_id = :user_id AND key_name = :key_name");
            $stmt->execute([':user_id' => $user_id, ':key_name' => $key_name]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            // Wenn der Artikel bereits zugewiesen wurde (status = 1), überspringe die Bestandsprüfung
            if ($existing && $existing['status'] == 1) {
                continue; // Artikel wurde bereits zugewiesen, überspringen
            }

            // Prüfe den aktuellen Bestand des Artikels (nur wenn der Artikel noch nicht zugewiesen wurde oder zurückgegeben ist)
            // Prüfe den aktuellen Bestand des Artikels
            $stmt = $conn->prepare("SELECT stock FROM ausruestungstypen WHERE key_name = :key_name");
            $stmt->execute([':key_name' => $key_name]);
            $stock = $stmt->fetchColumn();

            if ($stock <= 0) {
                echo json_encode(['success' => false, 'message' => 'Nicht genügend Artikel auf Lager!']);
                exit;
            }
        }
    }

    // Schleife durch die Änderungen und speichere sie in der 'benutzer_ausruestung'-Tabelle
    foreach ($ausruestung as $key_name => $status) {
        // Überprüfe, ob der Benutzer bereits diesen Gegenstand in der Tabelle 'benutzer_ausruestung' hat
        $stmt = $conn->prepare("SELECT status FROM benutzer_ausruestung WHERE user_id = :user_id AND key_name = :key_name");
        $stmt->execute([':user_id' => $user_id, ':key_name' => $key_name]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        // Wenn der Artikel zurückgegeben wird (Status 0) und der Artikel aktuell zugewiesen ist
        if ($status == 0 && $existing && $existing['status'] == 1) {
            // Setze den Artikel auf "zurückgegeben" (status = 0)
            $stmt = $conn->prepare("UPDATE benutzer_ausruestung SET status = 0 WHERE user_id = :user_id AND key_name = :key_name");
            $stmt->execute([':user_id' => $user_id, ':key_name' => $key_name]);

            // Bestands-Update in der Tabelle ausruestungstypen (Stock erhöhen)
            $stmt = $conn->prepare("UPDATE ausruestungstypen SET stock = stock + 1 WHERE key_name = :key_name");
            $stmt->execute([':key_name' => $key_name]);

            // History-Eintrag mit Aktion "Zurückgabe"
            $stmt = $conn->prepare("INSERT INTO ausruestung_history (user_id, key_name, action, stock_change, editor_name) VALUES (:user_id, :key_name, 'Zurückgabe', 1, :editor_name)");
            $stmt->execute([':user_id' => $user_id, ':key_name' => $key_name, ':editor_name' => $user_name]);
        }

        // Wenn der Artikel ausgegeben wird (Status 1)
        elseif ($status == 1) {
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

    // Log-Eintrag für die Änderungen
    logAction('UPDATE', 'ausruestung', 'user_id: ' . $user_id . ', changes: ' . json_encode($ausruestung) . ', edited_by: ' . $_SESSION['user_id']);
} catch (Exception $e) {
    // Bei Fehlern wird die Transaktion zurückgerollt
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
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
