<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

include 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Überprüfen, ob das CSRF-Token gültig ist
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false, 'message' => 'Ungültiges CSRF-Token']);
        exit;
    }

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']) && $_POST['remember'] === 'true';

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Bitte alle Felder ausfüllen.']);
        exit;
    }

    try {
        // Benutzer anhand der E-Mail-Adresse suchen (Kunde)
        $stmt = $conn->prepare("SELECT * FROM kunden WHERE umail = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Session-Daten setzen
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['name'];
            $_SESSION['email'] = $user['umail'];
            $_SESSION['role'] = 'customer'; // Setze Rolle für Kunden

            // Sitzungsinformationen in der Datenbank speichern
            $session_id = session_id();       // Die aktuelle Session-ID
            $ip_address = $_SERVER['REMOTE_ADDR'];  // IP-Adresse des Benutzers
            $query = "INSERT INTO kunden_sessions (user_id, session_id, ip_address) VALUES (:user_id, :session_id, :ip_address)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':user_id', $_SESSION['user_id']);
            $stmt->bindParam(':session_id', $session_id);
            $stmt->bindParam(':ip_address', $ip_address);
            $stmt->execute();

            if ($remember) {
                // Token für "Remember Me"-Funktion erstellen
                $token = bin2hex(random_bytes(32));
                setcookie('remember_me', $token, time() + 86400 * 30, '/'); // 30 Tage gültig

                // Token in der Datenbank speichern
                $query = "INSERT INTO remember_me_tokens (user_id, token) VALUES (:user_id, :token)";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':user_id', $_SESSION['user_id']);
                $stmt->bindParam(':token', $token);
                $stmt->execute();
            }

            // Log-Eintrag für den Login
            logAction('LOGIN', 'kunden', 'Benutzer eingeloggt: ' . $email . ', IP-Adresse: ' . $ip_address);

            echo json_encode(['success' => true, 'message' => 'Login erfolgreich']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Ungültige E-Mail oder Passwort']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
    }
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
