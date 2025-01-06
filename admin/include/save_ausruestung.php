<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Benutzerdaten aus POST holen
    $user_id = $_POST['user_id'] ?? $_GET['id'] ?? null;
    $letzte_spind_kontrolle = $_POST['letzte_spind_kontrolle'] ?? null;
    $notiz = $_POST['notiz'] ?? null;

    // JSON-String dekodieren und Fehler prüfen
    if (isset($_POST['ausruestung'])) {
        $ausruestung = json_decode($_POST['ausruestung'], true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(['success' => false, 'message' => 'Ungültiges JSON: ' . json_last_error_msg()]);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Fehlende Ausrüstungsdaten']);
        exit;
    }

    // Überprüfen, ob es sich um ein Array handelt
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
        // Bestehenden Code beibehalten, aber SQL-Fehler protokollieren
        foreach ($ausruestung as $key_name => $status) {
            if ($status == 1 || $status == 0) {
                $stmt = $conn->prepare("SELECT * FROM ausruestungstypen WHERE key_name = :key_name");
                $stmt->execute([':key_name' => $key_name]);
                $ausruestungItem = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($ausruestungItem) {
                    $new_stock = $ausruestungItem['stock'] + ($status == 1 ? -1 : 1); // Bestandsänderung

                    // Bestandsänderung speichern
                    $stmt = $conn->prepare("UPDATE ausruestungstypen SET stock = :stock WHERE key_name = :key_name");
                    if (!$stmt->execute([
                        ':stock' => $new_stock,
                        ':key_name' => $key_name
                    ])) {
                        // Fehler loggen
                        $error = $stmt->errorInfo();
                        echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern der Bestandsänderung: ' . $error[2]]);
                        exit;
                    }

                    // Historie der Bestandsänderung speichern
                    $stmt = $conn->prepare("INSERT INTO ausruestung_history (user_id, key_name, action, stock_change, editor_name) 
                                           VALUES (:user_id, :key_name, :action, :stock_change, :editor_name)");
                    if (!$stmt->execute([
                        ':user_id' => $user_id,
                        ':key_name' => $key_name,
                        ':action' => ($status == 1 ? 'Ausgegeben' : 'Zurückgegeben'),
                        ':stock_change' => ($status == 1 ? -1 : 1),
                        ':editor_name' => $editor_name
                    ])) {
                        // Fehler loggen
                        $error = $stmt->errorInfo();
                        echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern der Historie: ' . $error[2]]);
                        exit;
                    }
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
