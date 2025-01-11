<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

include 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']) && $_POST['remember'] === 'true';

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Bitte alle Felder ausfüllen.']);
        exit;
    }

    try {
        // Benutzer anhand der E-Mail-Adresse suchen
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Prüfen, ob der Benutzer im Admin-Bereich ist
            if ($user['admin_bereich'] != 1) {
                echo json_encode(['success' => false, 'message' => 'Zugang verweigert. Sie haben keinen Administrator-Zugang.']);
                exit;
            }

            // Session-Daten setzen
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['profile_image'] = $user['profile_image']; // Profilbild speichern

            // Sitzungsinformationen in der user_sessions-Tabelle speichern
            $session_id = session_id();       // Die aktuelle Session-ID
            $ip_address = $_SERVER['REMOTE_ADDR'];  // IP-Adresse des Benutzers
            $query = "INSERT INTO user_sessions (user_id, session_id, ip_address) VALUES (:user_id, :session_id, :ip_address)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':user_id', $_SESSION['user_id']);
            $stmt->bindParam(':session_id', $session_id);
            $stmt->bindParam(':ip_address', $ip_address);
            $stmt->execute();

            // Loggen des Logins
            logAction('LOGIN', 'user_sessions', 'user_id: ' . $_SESSION['user_id'] . ', session_id: ' . $session_id);

            if ($remember) {
                // Token für "Remember Me"-Funktion erstellen
                $token = bin2hex(random_bytes(32));
                setcookie('remember_me', $token, time() + 86400 * 30, '/', '', true, true); // 30 Tage gültig, HttpOnly und Secure

                // Token in der Datenbank speichern
                $stmt = $conn->prepare("UPDATE users SET remember_token = :token, token_expiry = DATE_ADD(NOW(), INTERVAL 30 DAY) WHERE id = :id");
                $stmt->execute([':token' => $token, ':id' => $user['id']]);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Login erfolgreich.',
                'session_data' => [
                    'user_id' => $_SESSION['user_id'],
                    'username' => $_SESSION['username'],
                    'email' => $_SESSION['email']
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Ungültige E-Mail-Adresse oder Passwort.']);
        }
    } catch (Exception $e) {
        error_log('Database error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Fehler beim Login: ' . $e->getMessage()]);
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
