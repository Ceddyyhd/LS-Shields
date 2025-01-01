<?php
include 'db.php'; // Datenbankverbindung

session_start();

// Sicherstellen, dass der Benutzer eingeloggt ist
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Benutzer ist nicht eingeloggt.']);
    exit;
}

$user_id = $_SESSION['user_id']; // Benutzer-ID aus der Session holen

// Benutzerdaten aus dem Formular
$umail = $_POST['umail'] ?? '';
$name = $_POST['name'] ?? '';
$nummer = $_POST['nummer'] ?? '';
$kontonummer = $_POST['kontonummer'] ?? '';
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';

// Sicherstellen, dass alle notwendigen Felder ausgefüllt sind
if (empty($umail) || empty($name) || empty($nummer) || empty($kontonummer)) {
    echo json_encode(['success' => false, 'message' => 'Alle Felder müssen ausgefüllt werden.']);
    exit;
}

// Passwortänderung überprüfen
if (!empty($password) && $password !== $password_confirm) {
    echo json_encode(['success' => false, 'message' => 'Passwörter stimmen nicht überein.']);
    exit;
}

// Falls das Passwort geändert wird, es verschlüsseln
if (!empty($password)) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
} else {
    $hashed_password = null; // Falls kein Passwort geändert wird, bleibt es unverändert
}

// SQL zum Aktualisieren der Benutzerdaten
$query = "UPDATE kunden SET umail = :umail, name = :name, nummer = :nummer, kontonummer = :kontonummer" .
         ($hashed_password ? ", password = :password" : "") . " WHERE id = :user_id";

$stmt = $conn->prepare($query);
$params = [
    ':umail' => $umail,
    ':name' => $name,
    ':nummer' => $nummer,
    ':kontonummer' => $kontonummer,
    ':user_id' => $user_id,
];

if ($hashed_password) {
    $params[':password'] = $hashed_password;
}

$stmt->execute($params);

echo json_encode(['success' => true, 'message' => 'Daten erfolgreich aktualisiert.']);
?>
