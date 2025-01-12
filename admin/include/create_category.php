<?php
include 'security_check.php'; // Sicherheitsprüfung für diese Datei

// Fehlerprotokollierung aktivieren (für Debugging)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Datenbankverbindung einbinden
include 'db.php';

// Überprüfen, ob der Benutzer eingeloggt ist
session_start();

// Wenn der Benutzer nicht eingeloggt ist, dann nichts tun
if (!isset($_SESSION['user_id'])) {
    die("Kein Benutzer eingeloggt.");
}

// Empfangen des Kategoriernamen
$new_category_name = $_POST['name'];

// Überprüfen, ob der Name leer ist
if (empty($new_category_name)) {
    echo json_encode(['success' => false, 'message' => 'Kategorie Name darf nicht leer sein.']);
    exit;
}

try {
    // SQL-Abfrage, um die neue Kategorie hinzuzufügen
    $stmt = $conn->prepare("INSERT INTO ausruestungskategorien (name) VALUES (:name)");
    $stmt->execute([
        ':name' => $new_category_name
    ]);

    // Erfolgreiche Antwort zurückgeben
    echo json_encode(['success' => true, 'message' => 'Kategorie erfolgreich hinzugefügt.']);

} catch (PDOException $e) {
    // Fehlerbehandlung
    echo json_encode(['success' => false, 'message' => 'Fehler beim Hinzufügen der Kategorie: ' . $e->getMessage()]);
}
?>
