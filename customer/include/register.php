<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Überprüfen, ob die Anfrage eine POST-Anfrage ist
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'db.php';  // Verbindungsdatei zu deiner DB

    $name = $_POST['name'] ?? '';
    $umail = $_POST['umail'] ?? '';
    $password = $_POST['password'] ?? '';
    $repassword = $_POST['repassword'] ?? '';
    $invite_code = $_POST['invite_code'] ?? '';  // Einladungscode

    // Überprüfen, ob alle Felder ausgefüllt sind
    if (empty($name) || empty($umail) || empty($password) || empty($repassword) || empty($invite_code)) {
        echo json_encode(['success' => false, 'message' => 'Bitte füllen Sie alle Felder aus.']);
        exit;
    }

    // Überprüfen, ob die Passwörter übereinstimmen
    if ($password !== $repassword) {
        echo json_encode(['success' => false, 'message' => 'Die Passwörter stimmen nicht überein.']);
        exit;
    }

    // Überprüfen, ob die E-Mail bereits existiert
    $stmt = $conn->prepare("SELECT * FROM kunden WHERE umail = :umail");
    $stmt->execute([':umail' => $umail]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode(['success' => false, 'message' => 'Diese E-Mail ist bereits registriert.']);
        exit;
    }

    // Einladungscode überprüfen
    $stmt = $conn->prepare("SELECT * FROM invites WHERE invite_code = :invite_code");
    $stmt->execute([':invite_code' => $invite_code]);
    $invite = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$invite) {
        echo json_encode(['success' => false, 'message' => 'Der Einladungscode ist ungültig.']);
        exit;
    }

    // Passwort-Hash erstellen
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Benutzer in der Datenbank speichern
    $stmt = $conn->prepare("INSERT INTO kunden (name, umail, password) VALUES (:name, :umail, :password)");
    $stmt->execute([
        ':name' => $name,
        ':umail' => $umail,
        ':password' => $passwordHash
    ]);

    // Einladungscode als verwendet markieren
    $stmt = $conn->prepare("UPDATE invites SET used = 1 WHERE invite_code = :invite_code");
    $stmt->execute([':invite_code' => $invite_code]);

    // Log-Eintrag für die Registrierung
    logAction('REGISTER', 'kunden', 'Neuer Kunde registriert: E-Mail: ' . $umail);

    echo json_encode(['success' => true, 'message' => 'Registrierung erfolgreich']);
}

// Funktion zum Loggen von Aktionen
function logAction($action, $table, $details) {
    global $conn;

    // SQL-Abfrage zum Einfügen des Log-Eintrags
    $stmt = $conn->prepare("INSERT INTO logs (action, table_name, details, user_id, timestamp) VALUES (:action, :table_name, :details, NULL, NOW())");
    $stmt->bindParam(':action', $action, PDO::PARAM_STR);
    $stmt->bindParam(':table_name', $table, PDO::PARAM_STR);
    $stmt->bindParam(':details', $details, PDO::PARAM_STR);
    $stmt->execute();
}
?>
