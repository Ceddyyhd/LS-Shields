<?php
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
        // Benutzer anhand der E-Mail-Adresse suchen (Mitarbeiter)
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Session-Daten setzen
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = 'admin'; // Setze Rolle für Admin

            if ($remember) {
                // Token für "Remember Me"-Funktion erstellen
                $token = bin2hex(random_bytes(32));
                setcookie('remember_me', $token, time() + 86400 * 30, '/'); // 30 Tage gültig

                // Token in der Mitarbeiter-Datenbank speichern
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
                    'role' => $_SESSION['role']
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
