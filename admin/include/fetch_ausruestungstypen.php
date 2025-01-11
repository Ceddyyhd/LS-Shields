<?php
require_once 'db.php'; // Deine DB-Verbindungsdatei

// Beispiel für die Berechtigung des Benutzers
session_start();
header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'error' => 'Ungültiges CSRF-Token']);
    exit;
}

// Funktion, die überprüft, ob der Benutzer eine bestimmte Berechtigung hat
function has_permission($permission) {
    return isset($_SESSION['permissions'][$permission]) && $_SESSION['permissions'][$permission];
}

try {
    // SQL-Abfrage, um alle Ausrüstungstypen abzurufen
    $sql = "SELECT id, key_name, display_name, description, stock, category FROM ausruestungstypen ORDER BY category";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Alle Ergebnisse in einem Array speichern
    $ausruestungstypen = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Überprüfen, ob Daten vorhanden sind
    if ($ausruestungstypen) {
        // Füge Berechtigungen hinzu
        foreach ($ausruestungstypen as &$ausruestung) {
            $ausruestung['can_edit'] = has_permission('ausruestungstyp_update');
            $ausruestung['can_delete'] = has_permission('ausruestungstyp_remove');
        }

        // Header setzen, um die Antwort als JSON zurückzugeben
        echo json_encode($ausruestungstypen);
    } else {
        echo json_encode(['success' => false, 'message' => 'Keine Ausrüstungstypen gefunden.']);
    }

} catch (PDOException $e) {
    // Fehlerbehandlung
    error_log('Datenbankfehler: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
}

$conn = null; // Verbindung schließen
?>
