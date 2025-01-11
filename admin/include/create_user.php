<?php
require_once 'db.php';

// Fehleranzeige für Debugging aktivieren
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Überprüfen, ob das CSRF-Token gültig ist
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header('Location: ../error.php');
        exit;
    }

    // Formular-Daten empfangen
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $umail = filter_input(INPUT_POST, 'umail', FILTER_SANITIZE_EMAIL);
    $nummer = filter_input(INPUT_POST, 'nummer', FILTER_SANITIZE_STRING);
    $kontonummer = filter_input(INPUT_POST, 'kontonummer', FILTER_SANITIZE_STRING);
    $admin_bereich = filter_input(INPUT_POST, 'admin_bereich', FILTER_VALIDATE_INT);
    $bewerber = filter_input(INPUT_POST, 'bewerber', FILTER_SANITIZE_STRING) ?? 'ja'; // Standardwert 'ja'

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
            ':role_id' => 11, // Standardmäßig Role-ID für "Bewerber"
            ':profile_image' => $profile_image,
            ':gekuendigt' => 'no_kuendigung',
            ':admin_bereich' => $admin_bereich,
            ':bewerber' => $bewerber
        ]);

        // Log-Eintrag für das Erstellen des Benutzers
        $user_id = $conn->lastInsertId();
        logAction('INSERT', 'users', 'user_id: ' . $user_id . ', created_by: ' . $_SESSION['user_id']);

        echo json_encode(['success' => true, 'message' => 'Benutzer wurde erfolgreich erstellt.']);
    } catch (PDOException $e) {
        error_log('Fehler beim Erstellen des Benutzers: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Fehler beim Erstellen des Benutzers: ' . $e->getMessage()]);
    }
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
