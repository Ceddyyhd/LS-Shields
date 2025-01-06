<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Benutzerdaten aus POST holen
    $user_id = $_POST['user_id'] ?? $_GET['id'] ?? null;
    $letzte_spind_kontrolle = $_POST['letzte_spind_kontrolle'] ?? null;
    $notiz = $_POST['notiz'] ?? null;

    // Debugging: Ausgabe der 'ausruestung' Daten aus POST
    var_dump($_POST['ausruestung']);  // Zeigt, was im POST-Request gesendet wird

    // Das JSON-Feld ausruestung dekodieren
    if (isset($_POST['ausruestung'])) {
        $ausruestung = json_decode($_POST['ausruestung'], true);  // Dekodierung des JSON
        
        // Überprüfen, ob die Dekodierung erfolgreich war
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(['success' => false, 'message' => 'Ungültiges JSON: ' . json_last_error_msg()]);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Fehlende Ausrüstungsdaten']);
        exit;
    }

    // Nun sicherstellen, dass es ein Array ist
    if (!is_array($ausruestung)) {
        echo json_encode(['success' => false, 'message' => 'Ausruestung ist kein Array']);
        exit;
    }

    // Berechtigungsprüfung
    if (!($_SESSION['permissions']['edit_employee'] ?? false)) {
        echo json_encode(['success' => false, 'message' => 'Keine Berechtigung, Änderungen vorzunehmen.']);
        exit;
    }

    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'Benutzer-ID fehlt.']);
        exit;
    }

    try {
        // Wenn der Wert für letzte_spind_kontrolle leer ist, setze ihn auf NULL
        $letzte_spind_kontrolle = empty($letzte_spind_kontrolle) ? null : $letzte_spind_kontrolle;

        // Überprüfen, ob es bereits einen Eintrag für diesen Benutzer gibt
        $stmt = $conn->prepare("SELECT id FROM spind_kontrolle_notizen WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        $existingEntry = $stmt->fetch(PDO::FETCH_ASSOC);

        // Benutzername für das Log
        $stmt = $conn->prepare("SELECT name FROM users WHERE id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $editor_name = $user['name'] ?? 'Unbekannt';

        // Wenn der Eintrag bereits existiert, aktualisieren
        if ($existingEntry) {
            $stmt = $conn->prepare("UPDATE spind_kontrolle_notizen 
                                    SET letzte_spind_kontrolle = :letzte_spind_kontrolle, notizen = :notizen 
                                    WHERE user_id = :user_id");
            $stmt->execute([
                ':letzte_spind_kontrolle' => $letzte_spind_kontrolle,
                ':notizen' => $notiz,
                ':user_id' => $user_id
            ]);
        } else {
            // Neuen Eintrag in die Tabelle einfügen
            $stmt = $conn->prepare("INSERT INTO spind_kontrolle_notizen (user_id, letzte_spind_kontrolle, notizen) 
                                    VALUES (:user_id, :letzte_spind_kontrolle, :notizen)");
            $stmt->execute([
                ':user_id' => $user_id,
                ':letzte_spind_kontrolle' => $letzte_spind_kontrolle,
                ':notizen' => $notiz
            ]);
        }

        // Log für die Änderung oder Erstellung
        $stmt = $conn->prepare("INSERT INTO spind_kontrolle_logs (user_id, editor_name, action) 
                                VALUES (:user_id, :editor_name, :action)");
        $stmt->execute([
            ':user_id' => $user_id,
            ':editor_name' => $editor_name,
            ':action' => $existingEntry ? 'Aktualisiert' : 'Erstellt'
        ]);

        // Bestandsänderungen und Historie nur für geänderte Ausrüstungen speichern
        foreach ($ausruestung as $key_name => $status) {
            // Überprüfen, ob der Status der Ausrüstung geändert wurde
            if ($status == 1 || $status == 0) {
                $stmt = $conn->prepare("SELECT * FROM ausruestungstypen WHERE key_name = :key_name");
                $stmt->execute([':key_name' => $key_name]);
                $ausruestungItem = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($ausruestungItem) {
                    $new_stock = $ausruestungItem['stock'] + ($status == 1 ? -1 : 1); // Bestandsänderung

                    // Bestandsänderung speichern
                    $stmt = $conn->prepare("UPDATE ausruestungstypen SET stock = :stock WHERE key_name = :key_name");
                    $stmt->execute([
                        ':stock' => $new_stock,
                        ':key_name' => $key_name
                    ]);

                    // Historie der Bestandsänderung speichern
                    $stmt = $conn->prepare("INSERT INTO ausruestung_history (user_id, key_name, action, stock_change, editor_name) 
                                            VALUES (:user_id, :key_name, :action, :stock_change, :editor_name)");
                    $stmt->execute([
                        ':user_id' => $user_id,
                        ':key_name' => $key_name,
                        ':action' => ($status == 1 ? 'Ausgegeben' : 'Zurückgegeben'),
                        ':stock_change' => ($status == 1 ? -1 : 1),
                        ':editor_name' => $editor_name
                    ]);
                }
            }
        }

        echo json_encode(['success' => true, 'message' => 'Änderungen gespeichert.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern: ' . $e->getMessage()]);
    }
    exit;
}
?>
