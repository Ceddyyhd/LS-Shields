<?php
include 'security_check.php'; // Sicherheitsprüfung für diese Datei

include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null; // ID des Benutzers, dessen Rang geändert werden soll
    $new_role_id = $_POST['role_id'] ?? null;

    // Berechtigungsprüfung
    if (!($_SESSION['permissions']['change_rank'] ?? false)) {
        echo json_encode(['success' => false, 'message' => 'Keine Berechtigung, den Rang zu ändern.']);
        exit;
    }

    if (!$user_id || !$new_role_id) {
        echo json_encode(['success' => false, 'message' => 'Benutzer-ID oder neuer Rang fehlt.']);
        exit;
    }

    try {
        // Zielbenutzer prüfen
        $stmt = $conn->prepare("SELECT role_id FROM users WHERE id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        $old_role_id = $stmt->fetchColumn();

        if ($old_role_id === false) { // Kein Eintrag gefunden
            echo json_encode(['success' => false, 'message' => 'Zielbenutzer nicht gefunden.']);
            exit;
        }

        // Den Wert des neuen Rangs abrufen
        $stmt = $conn->prepare("SELECT value FROM roles WHERE id = :role_id");
        $stmt->execute([':role_id' => $new_role_id]);
        $new_role_value = $stmt->fetchColumn();

        if (!$new_role_value) { // Wenn der neue Rang nicht existiert
            echo json_encode(['success' => false, 'message' => 'Ungültiger Rang angegeben.']);
            exit;
        }

        // Den Wert des aktuellen Benutzers abrufen
        $stmt = $conn->prepare("SELECT roles.value FROM users JOIN roles ON users.role_id = roles.id WHERE users.id = :current_user_id");
        $stmt->execute([':current_user_id' => $_SESSION['user_id']]);
        $current_user_value = $stmt->fetchColumn();

        if (!$current_user_value || $new_role_value > $current_user_value) {
            echo json_encode(['success' => false, 'message' => 'Sie können keine Gruppen mit einem höheren Rang als Ihrem eigenen ändern.']);
            exit;
        }

        // Rangänderung durchführen
        $editor_name = $_SESSION['username'] ?? 'Unbekannt';
        $stmt = $conn->prepare("UPDATE users SET role_id = :new_role_id, rank_last_changed_by = :changed_by WHERE id = :user_id");
        $stmt->execute([
            ':new_role_id' => $new_role_id,
            ':changed_by' => $editor_name,
            ':user_id' => $user_id,
        ]);

        // Log in die Tabelle `rank_change_logs` schreiben
        $stmt = $conn->prepare("INSERT INTO rank_change_logs (user_id, old_role_id, new_role_id, changed_by) 
                                VALUES (:user_id, :old_role_id, :new_role_id, :changed_by)");
        $stmt->execute([
            ':user_id' => $user_id,
            ':old_role_id' => $old_role_id,
            ':new_role_id' => $new_role_id,
            ':changed_by' => $editor_name,
        ]);

        echo json_encode(['success' => true, 'message' => 'Rang erfolgreich geändert.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern: ' . $e->getMessage()]);
    }
}
?>
