<?php
session_start();
include 'db.php';  // Hier wird die Datei mit der Datenbankverbindung eingebunden

// Nur Admins sollten die Sitzung eines Benutzers beenden dürfen
if ($_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unzureichende Berechtigung']);
    exit;
}

// Überprüfe, ob eine Benutzer-ID übergeben wurde
if (isset($_POST['user_id']) && is_numeric($_POST['user_id'])) {
    $user_id_to_logout = $_POST['user_id'];

    // Stelle sicher, dass der Admin nicht sich selbst logoutet
    if ($user_id_to_logout == $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'Admin kann nicht sich selbst ausloggen.']);
        exit;
    }

    try {
        // 1. Lösche die Sitzung des Benutzers aus der `user_sessions`-Tabelle
        $query = "DELETE FROM kunden_sessions WHERE user_id = :user_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id_to_logout);
        $stmt->execute();

        // 2. Setze das 'remember_token' auf NULL in der `kunden`-Tabelle
        $query = "UPDATE kunden SET remember_token = NULL WHERE id = :user_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id_to_logout);
        $stmt->execute();

        // 3. Optional: Entferne auch das 'remember_me' Cookie, falls gesetzt
        setcookie('remember_me', '', time() - 3600, '/');

        // 4. Wenn der Admin den Logout für einen Benutzer ausführt, aber nicht für sich selbst
        if ($_SESSION['user_id'] != $user_id_to_logout) {
            echo json_encode(['success' => true, 'message' => 'Benutzer wurde erfolgreich abgemeldet.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Admin-Session bleibt bestehen.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage']);
}
?>
