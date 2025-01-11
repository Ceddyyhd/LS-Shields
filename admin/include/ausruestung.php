<?php
session_start();
require_once 'db.php'; // Datenbankverbindung

header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header('Location: ../error.php');
    exit;
}

// Berechtigungsprüfung
$canEdit = $_SESSION['permissions']['edit_employee'] ?? false;
$editor_name = $_SESSION['user_name'] ?? 'Unbekannt'; // Fallback-Wert, falls 'user_name' nicht existiert

// Benutzer-ID aus POST-Daten abrufen
$user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Ungültige Benutzer-ID']);
    exit;
}

try {
    // Ausrüstungstypen und Benutzer-Ausrüstung abrufen
    $stmt = $conn->prepare("SELECT key_name, display_name, category FROM ausruestungstypen ORDER BY category");
    $stmt->execute();
    $ausruestungstypen = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Benutzer-Ausrüstung abrufen
    $stmt = $conn->prepare("SELECT key_name, status FROM benutzer_ausruestung WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $benutzerAusrüstung = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Abrufen der letzten Spind Kontrolle und Notiz für den Benutzer
    $stmt = $conn->prepare("SELECT letzte_spind_kontrolle, notizen FROM spind_kontrolle_notizen WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $benutzerSpind_Kontrolle = $stmt->fetch(PDO::FETCH_ASSOC);

    // Falls keine Daten vorhanden sind, Standardwerte setzen
    $letzte_spind_kontrolle = $benutzerSpind_Kontrolle['letzte_spind_kontrolle'] ?? '';
    $notizen = $benutzerSpind_Kontrolle['notizen'] ?? '';

    // Benutzer-Ausrüstung in ein Array umwandeln
    $userAusrüstung = [];
    foreach ($benutzerAusrüstung as $item) {
        $userAusrüstung[$item['key_name']] = (int)$item['status'];
    }

    // Nach Kategorien gruppieren
    $categories = [];
    foreach ($ausruestungstypen as $item) {
        $categories[$item['category']][] = $item;
    }

    // Loggen des Abrufs
    logAction('SELECT', 'ausruestung', 'user_id: ' . $user_id);

    // Rückgabe der abgerufenen Daten als Array
    echo json_encode([
        'success' => true,
        'canEdit' => $canEdit,
        'categories' => $categories,
        'userAusrüstung' => $userAusrüstung,
        'letzte_spind_kontrolle' => $letzte_spind_kontrolle,
        'notizen' => $notizen
    ]);
} catch (PDOException $e) {
    error_log('Fehler beim Abrufen der Ausrüstung: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Fehler beim Abrufen der Ausrüstung: ' . $e->getMessage()]);
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
