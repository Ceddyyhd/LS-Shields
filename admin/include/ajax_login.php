<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
$_SESSION['username'] = $username; // Setze den Benutzernamen

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

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];

        if ($remember) {
            // Erstelle ein zufälliges Token
            $token = bin2hex(random_bytes(32));
            setcookie('remember_me', $token, time() + 86400 * 30, '/'); // 30 Tage gültig
        
            // Token in der Datenbank speichern
            $stmt = $conn->prepare("UPDATE users SET remember_token = :token WHERE id = :id");
            $stmt->execute([':token' => $token, ':id' => $user['id']]);
        }

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ungültige Anmeldedaten.']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage.']);

