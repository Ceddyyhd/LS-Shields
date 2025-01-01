<?php
session_start();
include 'db.php';  // Hier wird die Datei mit der Datenbankverbindung eingebunden

// Nur Admins dürfen Benutzer abmelden
if ($_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unzureichende Berechtigung']);
    exit;
}

// Benutzer-ID prüfen
if (isset($_POST['user_id']) && is_numeric($_POST['user_id'])) {
    $user_id_to_logout = $_POST['user_id'];

    // Sicherstellen, dass der Admin nicht sich selbst logoutet
    if ($user_id_to_logout == $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'Admin kann nicht sich selbst ausloggen.']);
        exit;
    }

    try {
        // 1. Sitzung des Benutzers aus der `user_sessions`-Tabelle entfernen
        $query = "DELETE FROM user_sessions WHERE user_id = :user_id";
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

        // 4. Falls der Benutzer der gerade eingeloggte Admin ist, sicherstellen, dass auch seine Session gelöscht wird
        if ($_SESSION['user_id'] == $user_id_to_logout) {
            session_unset();  // Löscht alle Session-Daten
            session_destroy();  // Zerstört die Session
            setcookie('PHPSESSID', '', time() - 3600, '/');  // Löscht den PHP-Session-Cookie
            header('Location: login.php');  // Weiterleitung zur Login-Seite
            exit;
        }

        echo json_encode(['success' => true, 'message' => 'Benutzer wurde erfolgreich abgemeldet.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage']);
}
?>
