<?php
include 'security_check.php'; // Sicherheitsprüfung für diese Datei

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

include 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']) && $_POST['remember'] === 'true';

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Bitte alle Felder ausfüllen.']);
        exit;
    }

    try {
        // Benutzer anhand der E-Mail-Adresse suchen und die Rolle über einen JOIN abfragen
        $stmt = $conn->prepare("SELECT u.*, r.level, r.value FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.email = :email");
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
            $_SESSION['user_role'] = $user['level']; // Benutzerrolle (level) in der Session speichern
            $_SESSION['user_role_value'] = $user['value']; // Benutzerrolle (value) in der Session speichern

            // Sitzungsinformationen in der user_sessions-Tabelle speichern
            $session_id = session_id();       // Die aktuelle Session-ID
            $ip_address = $_SERVER['REMOTE_ADDR'];  // IP-Adresse des Benutzers
            $query = "INSERT INTO user_sessions (user_id, session_id, ip_address) VALUES (:user_id, :session_id, :ip_address)";
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
                $stmt = $conn->prepare("UPDATE users SET remember_token = :token WHERE id = :id");
                $stmt->execute([':token' => $token, ':id' => $user['id']]);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Login erfolgreich.',
                'session_data' => [
                    'user_id' => $_SESSION['user_id'],
                    'username' => $_SESSION['username'],
                    'email' => $_SESSION['email'],
                    'user_role' => $_SESSION['user_role'],
                    'user_role_value' => $_SESSION['user_role_value'] // Rolle in den Sitzungsdaten zurückgeben
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Ungültige Anmeldedaten.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
    }
    exit;
}

// Falls die Anfrage keine POST-Anfrage ist
echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage.']);
