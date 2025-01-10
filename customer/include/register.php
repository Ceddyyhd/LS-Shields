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

    // Einladungscode löschen, wenn er verwendet wurde
    $stmt = $conn->prepare("DELETE FROM invites WHERE invite_code = :invite_code");
    $stmt->execute([':invite_code' => $invite_code]);

    // Passwortrücksetzung und Registrierung
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // SQL-Statement, um den neuen Benutzer in der Datenbank zu speichern
    try {
        $stmt = $conn->prepare("INSERT INTO kunden (name, umail, password) VALUES (:name, :umail, :password)");
        $stmt->execute([
            ':name' => $name,
            ':umail' => $umail,
            ':password' => $hashedPassword
        ]);

        echo json_encode(['success' => true, 'message' => 'Registrierung erfolgreich!']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage.']);
}
