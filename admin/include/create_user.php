<?php
require_once 'db.php';

// Fehleranzeige für Debugging aktivieren
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Formular-Daten empfangen
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $umail = $_POST['umail'] ?? '';
    $nummer = $_POST['nummer'] ?? null;
    $kontonummer = $_POST['kontonummer'] ?? null;
    $admin_bereich = $_POST['admin_bereich'] ?? 0;
    $bewerber = $_POST['bewerber'] ?? 'ja'; // Standardwert 'ja'

    // Passwort kann null sein, oder ein vorgegebener Wert (z.B. 'N/A')
    $password = 'N/A'; // Du kannst hier ein Passwort generieren oder leer lassen

    // Das Profilbild-Standardbild setzen
    $profile_image = 'uploads/profile_images/standard.png'; // Standardbild

    try {
        // Benutzer in der Datenbank speichern
        $stmt = $conn->prepare("
            INSERT INTO users (email, umail, name, nummer, kontonummer, password, role_id, profile_image, gekuendigt, admin_bereich, bewerber) 
            VALUES (:email, :umail, :name, :nummer, :kontonummer, :password, :role_id, :profile_image, :gekuendigt, :admin_bereich, :bewerber)
        ");
        $stmt->execute([
            ':email' => $email,
            ':umail' => $umail,
            ':name' => $name,
            ':nummer' => $nummer,
            ':kontonummer' => $kontonummer,
            ':password' => password_hash($password, PASSWORD_DEFAULT), // Das Passwort wird gehasht
            ':role_id' => 2, // Standardmäßig Role-ID für "Bewerber"
            ':profile_image' => $profile_image,
            ':gekuendigt' => 'no_kuendigung',
            ':admin_bereich' => $admin_bereich,
            ':bewerber' => $bewerber
        ]);

        echo json_encode(['success' => true, 'message' => 'Benutzer wurde erfolgreich erstellt.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage.']);
?>
