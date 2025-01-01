<?php
session_start();

// Datenbankverbindung einbinden
include 'db.php';  // Sicherstellen, dass die DB-Verbindung korrekt eingebunden ist

// Nur Admins sollten die Sitzung eines Benutzers beenden dürfen
if ($_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unzureichende Berechtigung']);
    exit;
}

// Überprüfe, ob eine Benutzer-ID übergeben wurde
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

        // 4. Wenn der Admin den Logout für den gleichen Benutzer ausführt
        if ($_SESSION['user_id'] == $user_id_to_logout) {
            // Lösche alle Session-Daten
            session_unset();  // Löscht alle Session-Daten
            session_destroy();  // Zerstört die Session

            // Lösche das PHP-Session-Cookie
            setcookie('PHPSESSID', '', time() - 3600, '/');  // Löscht den PHP-Session-Cookie

            // Weiterleitung zur Login-Seite
            header('Location: login.php');  // Umleitung zur Login-Seite
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
