<?php
session_start();

// Datenbankverbindung einbinden
include 'db.php';  // Sicherstellen, dass die DB-Verbindung korrekt eingebunden ist
header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Ungültiges CSRF-Token']);
    exit;
}

// Sicherstellen, dass der Benutzer ein Admin ist, bevor eine andere Sitzung gelöscht wird
if ($_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unzureichende Berechtigung']);
    exit;
}

// Überprüfen, ob eine Benutzer-ID übergeben wurde
if (isset($_POST['user_id']) && is_numeric($_POST['user_id'])) {
    $user_id_to_logout = $_POST['user_id'];

    // Sicherstellen, dass der Admin nicht sich selbst logoutet
    if ($user_id_to_logout == $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'Admin kann nicht sich selbst ausloggen.']);
        exit;
    }

    try {
        // 1. Sitzung des Benutzers aus der `user_sessions`-Tabelle entfernen
        $query = "DELETE FROM kunden_sessions WHERE user_id = :user_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id_to_logout);
        $stmt->execute();

        // 2. Setze das 'remember_token' auf NULL in der `kunden`-Tabelle
        $query = "UPDATE kunden SET remember_token = NULL WHERE id = :user_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id_to_logout);
        $stmt->execute();

        // 3. Lösche das 'remember_me' Cookie, falls gesetzt
        setcookie('remember_me', '', time() - 3600, '/');  // Cookie löschen

        // 4. Falls der Admin nicht ausgeloggt werden soll, löschen wir seine Session nicht
        if ($_SESSION['user_id'] == $user_id_to_logout) {
            session_unset();  // Löscht alle Session-Daten
            session_destroy();  // Zerstört die Session
            setcookie('PHPSESSID', '', time() - 3600, '/');  // Löscht das PHP-Session-Cookie
            header('Location: index.html');  // Weiterleitung zur Login-Seite
            exit;
        }

        // Log-Eintrag für das Ausloggen
        logAction('LOGOUT', 'kunden', 'user_id: ' . $user_id_to_logout . ', logged_out_by: ' . $_SESSION['user_id']);

        echo json_encode(['success' => true, 'message' => 'Benutzer wurde erfolgreich abgemeldet.']);
    } catch (PDOException $e) {
        error_log('Fehler beim Abmelden des Benutzers: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Fehler beim Abmelden des Benutzers: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Keine Benutzer-ID übergeben.']);
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
