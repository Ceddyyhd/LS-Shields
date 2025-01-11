<?php
function restoreSessionIfRememberMe($conn) {
    if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
        $token = $_COOKIE['remember_me'];

        $stmt = $conn->prepare("SELECT id, name, email, token_expiry FROM users WHERE remember_token = :token");
        $stmt->execute([':token' => $token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Überprüfen, ob das Token abgelaufen ist
            if (strtotime($user['token_expiry']) < time()) {
                // Token ist abgelaufen, Cookie löschen
                setcookie('remember_me', '', time() - 3600, '/', '', true, true);
                return;
            }

            // Session-Daten setzen
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['name'];
            $_SESSION['email'] = $user['email'];

            // Session-ID regenerieren
            session_regenerate_id(true);

            // Token erneuern und in der Datenbank speichern
            $newToken = bin2hex(random_bytes(32));
            $newExpiry = date('Y-m-d H:i:s', strtotime('+30 days'));
            $stmt = $conn->prepare("UPDATE users SET remember_token = :token, token_expiry = :expiry WHERE id = :id");
            $stmt->execute([':token' => $newToken, ':expiry' => $newExpiry, ':id' => $user['id']]);

            // Neues Token im Cookie setzen
            setcookie('remember_me', $newToken, time() + 86400 * 30, '/', '', true, true); // 30 Tage gültig, HttpOnly und Secure
        }
    }
}
?>
