<?php
function restoreSessionIfRememberMe($conn) {
    if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
        $token = $_COOKIE['remember_me'];

        $stmt = $conn->prepare("SELECT id, name, email FROM users WHERE remember_token = :token");
        $stmt->execute([':token' => $token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Session-Daten setzen
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['name'];
            $_SESSION['email'] = $user['email'];
        }
    }
}
?>
