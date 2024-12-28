<?php
require_once 'db.php'; // Deine DB-Verbindungsdatei

// Beispiel für die Berechtigung des Benutzers
session_start();

// Funktion, die überprüft, ob der Benutzer eine bestimmte Berechtigung hat
function has_permission($permission) {
    return isset($_SESSION['permissions'][$permission]) && $_SESSION['permissions'][$permission];
}

try {
    // SQL-Abfrage, um alle Ausrüstungstypen abzurufen
    $sql = "SELECT id, key_name, display_name, category, description FROM ausruestungstypen";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $ausruestungstypen = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Füge Berechtigungen hinzu
    foreach ($ausruestungstypen as &$ausruestung) {
        // Berechtigungen für Bearbeiten und Löschen hinzufügen
        $ausruestung['can_edit'] = has_permission('ausruestungstyp_update');
        $ausruestung['can_delete'] = has_permission('ausruestungstyp_remove');
    }

    // Header setzen, um die Antwort als JSON zurückzugeben
    header('Content-Type: application/json');
    echo json_encode($ausruestungstypen);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
}

$conn = null; // Verbindung schließen
?>
