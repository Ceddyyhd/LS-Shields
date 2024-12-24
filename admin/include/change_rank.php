<?php
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

        // Prüfen, ob der neue Rang existiert
        $stmt = $conn->prepare("SELECT id FROM roles WHERE id = :role_id");
        $stmt->execute([':role_id' => $new_role_id]);
        if (!$stmt->fetchColumn()) {
            echo json_encode(['success' => false, 'message' => 'Ungültiger Rang angegeben.']);
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
