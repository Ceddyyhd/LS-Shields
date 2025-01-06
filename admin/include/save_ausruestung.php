<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Benutzerdaten aus POST holen
    $user_id = $_POST['user_id'] ?? null;
    $ausruestung = $_POST['ausruestung'] ?? []; // Liste der Ausrüstungen mit ihrem Status (0 oder 1)

    // Berechtigungsprüfung
    if (!($_SESSION['permissions']['edit_employee'] ?? false)) {
        echo json_encode(['success' => false, 'message' => 'Keine Berechtigung, Änderungen vorzunehmen.']);
        exit;
    }

    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'Benutzer-ID fehlt.']);
        exit;
    }

    // Überprüfen, ob ausruestung als JSON korrekt ist
    if (!isset($_POST['ausruestung'])) {
        echo json_encode(['success' => false, 'message' => 'Fehlende Ausrüstungsdaten']);
        exit;
    }

    // Dekodieren des JSON-Strings zu einem Array
    $ausruestung = json_decode($_POST['ausruestung'], true);

    // Prüfen, ob die Dekodierung erfolgreich war
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['success' => false, 'message' => 'Ungültiges JSON: ' . json_last_error_msg()]);
        exit;
    }

    // Überprüfen, ob es sich um ein Array handelt
    if (!is_array($ausruestung)) {
        echo json_encode(['success' => false, 'message' => 'Ausruestung ist kein Array']);
        exit;
    }

    // Benutzername für das Log sicherstellen
    $editor_name = $_SESSION['user_name'] ?? 'Unbekannt';  // Standardwert 'Unbekannt' falls nicht gesetzt

    try {
        // Bestandsänderungen und Historie nur für geänderte Ausrüstungen speichern
        foreach ($ausruestung as $key_name => $status) {
            // Überprüfen, ob der Status der Ausrüstung geändert wurde
            if ($status === 1 || $status === 0) {
                $stmt = $conn->prepare("SELECT * FROM ausruestungstypen WHERE key_name = :key_name");
                $stmt->execute([':key_name' => $key_name]);
                $ausruestungItem = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($ausruestungItem) {
                    $new_stock = $ausruestungItem['stock'] + ($status === 1 ? -1 : 1); // Bestandsänderung

                    // Bestandsänderung speichern
                    $stmt = $conn->prepare("UPDATE ausruestungstypen SET stock = :stock WHERE key_name = :key_name");
                    $stmt->execute([
                        ':stock' => $new_stock,
                        ':key_name' => $key_name
                    ]);

                    // Historie der Bestandsänderung speichern
                    $stmt = $conn->prepare("INSERT INTO ausruestung_history (user_id, key_name, action, stock_change, editor_name) 
                                            VALUES (:user_id, :key_name, :action, :stock_change, :editor_name)");

                    if ($stmt->execute([ 
                        ':user_id' => $user_id, 
                        ':key_name' => $key_name, 
                        ':action' => ($status === 1 ? 'Ausgegeben' : 'Zurückgegeben'), 
                        ':stock_change' => ($status === 1 ? -1 : 1), 
                        ':editor_name' => $editor_name 
                    ])) {
                        // Erfolgreiches Einfügen in die Historie
                    } else {
                        // Fehlerprotokollierung
                        $error = $stmt->errorInfo();
                    }
                }
            }
        }

        echo json_encode(['success' => true, 'message' => 'Änderungen gespeichert.']);
    } catch (Exception $e) {
        // Fehlerbehandlung
        echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern: ' . $e->getMessage()]);
    }
    exit;
}
?>
