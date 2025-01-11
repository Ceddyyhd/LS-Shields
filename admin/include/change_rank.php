<?php
include 'db.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Überprüfen, ob das CSRF-Token gültig ist
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header('Location: ../error.php');
        exit;
    }

    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT); // ID des Benutzers, dessen Rang geändert werden soll
    $new_role_id = filter_input(INPUT_POST, 'role_id', FILTER_VALIDATE_INT);

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

        // Rang des Zielbenutzers aktualisieren
        $stmt = $conn->prepare("UPDATE users SET role_id = :new_role_id WHERE id = :user_id");
        $stmt->execute([':new_role_id' => $new_role_id, ':user_id' => $user_id]);

        // Loggen der Rangänderung
        logAction('UPDATE', 'users', 'user_id: ' . $user_id . ', old_role_id: ' . $old_role_id . ', new_role_id: ' . $new_role_id);

        echo json_encode(['success' => true, 'message' => 'Rang erfolgreich geändert.']);
    } catch (PDOException $e) {
        error_log('Fehler beim Ändern des Rangs: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Fehler beim Ändern des Rangs: ' . $e->getMessage()]);
    }
    exit;
} else {
    header('Location: ../error.php');
    exit;
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
