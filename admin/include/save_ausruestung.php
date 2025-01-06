<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

include 'db.php';

$user_id = $_POST['user_id'];
$user_name = $_POST['user_name'];

if (isset($_POST['ausruestung']) && is_array($_POST['ausruestung'])) {
    $ausruestung = $_POST['ausruestung'];
} else {
    $ausruestung = [];
    $stmt = $conn->prepare("SELECT key_name FROM ausruestungstypen");
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($items as $item) {
        $ausruestung[$item['key_name']] = 0;  // Setze alle Artikel auf "zurückgegeben"
    }
}

session_start();
$canEdit = $_SESSION['permissions']['edit_employee'] ?? false;

if (!$canEdit) {
    echo json_encode(['success' => false, 'message' => 'Keine Berechtigung zum Bearbeiten.']);
    exit;
}

try {
    $conn->beginTransaction();

    foreach ($ausruestung as $key_name => $status) {
        // Überprüfe den aktuellen Bestand
        $stmt = $conn->prepare("SELECT stock FROM ausruestungstypen WHERE key_name = :key_name");
        $stmt->execute([':key_name' => $key_name]);
        $stock = $stmt->fetchColumn();

        // Wenn der Status auf "ausgegeben" gesetzt wird (1), prüfe, ob genug Bestand vorhanden ist
        if ($status == 1) {
            if ($stock <= 0) {
                echo json_encode(['success' => false, 'message' => 'Nicht genügend Artikel auf Lager!']);
                exit;
            }
        }

        // Überprüfe, ob der Artikel bereits im Benutzerbestand vorhanden ist
        $stmt = $conn->prepare("SELECT status FROM benutzer_ausruestung WHERE user_id = :user_id AND key_name = :key_name");
        $stmt->execute([':user_id' => $user_id, ':key_name' => $key_name]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        // Wenn der Artikel zurückgegeben werden soll
        if ($status == 0) {
            // Wenn der Artikel bereits ausgegeben war, wird er zurückgegeben
            if ($existing && $existing['status'] == 1) {
                // Bestands-Update: Artikel wird zurückgegeben (Stock + 1)
                $stmt = $conn->prepare("UPDATE benutzer_ausruestung SET status = 0 WHERE user_id = :user_id AND key_name = :key_name");
                $stmt->execute([':user_id' => $user_id, ':key_name' => $key_name]);

                // Bestands-Update in der Tabelle ausruestungstypen (Stock erhöhen)
                $stmt = $conn->prepare("UPDATE ausruestungstypen SET stock = stock + 1 WHERE key_name = :key_name");
                $stmt->execute([':key_name' => $key_name]);

                // History-Eintrag: Artikel zurückgegeben
                $stmt = $conn->prepare("INSERT INTO ausruestung_history (user_id, key_name, action, stock_change, editor_name) VALUES (:user_id, :key_name, 'Zurückgabe', 1, :editor_name)");
                $stmt->execute([
                    ':user_id' => $user_id,
                    ':key_name' => $key_name,
                    ':editor_name' => $user_name
                ]);
            }
        } elseif ($status == 1) {
            // Wenn der Artikel ausgegeben wird
            if ($existing) {
                // Wenn der Artikel bereits vorhanden war und Status 0 war, dann aktualisiere den Status
                if ($existing['status'] == 0) {
                    // Bestands-Update in der Tabelle ausruestungstypen (Stock verringern)
                    $stmt = $conn->prepare("UPDATE ausruestungstypen SET stock = stock - 1 WHERE key_name = :key_name");
                    $stmt->execute([':key_name' => $key_name]);

                    // Bestandsänderung in der benutzer_ausruestung (Status wird auf 1 gesetzt)
                    $stmt = $conn->prepare("UPDATE benutzer_ausruestung SET status = 1 WHERE user_id = :user_id AND key_name = :key_name");
                    $stmt->execute([':user_id' => $user_id, ':key_name' => $key_name]);

                    // History-Eintrag: Artikel wurde ausgegeben
                    $stmt = $conn->prepare("INSERT INTO ausruestung_history (user_id, key_name, action, stock_change, editor_name) VALUES (:user_id, :key_name, 'Hinzufügung', -1, :editor_name)");
                    $stmt->execute([
                        ':user_id' => $user_id,
                        ':key_name' => $key_name,
                        ':editor_name' => $user_name
                    ]);
                }
            } else {
                // Wenn der Artikel noch nicht vergeben wurde, setze ihn als ausgegeben
                $stmt = $conn->prepare("INSERT INTO benutzer_ausruestung (user_id, key_name, status) VALUES (:user_id, :key_name, 1)");
                $stmt->execute([':user_id' => $user_id, ':key_name' => $key_name]);

                // Bestands-Update in der Tabelle ausruestungstypen (Stock verringern)
                $stmt = $conn->prepare("UPDATE ausruestungstypen SET stock = stock - 1 WHERE key_name = :key_name");
                $stmt->execute([':key_name' => $key_name]);

                // History-Eintrag: Artikel wurde ausgegeben
                $stmt = $conn->prepare("INSERT INTO ausruestung_history (user_id, key_name, action, stock_change, editor_name) VALUES (:user_id, :key_name, 'Hinzufügung', -1, :editor_name)");
                $stmt->execute([
                    ':user_id' => $user_id,
                    ':key_name' => $key_name,
                    ':editor_name' => $user_name
                ]);
            }
        }
    }

    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Änderungen wurden erfolgreich gespeichert!']);
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
}
