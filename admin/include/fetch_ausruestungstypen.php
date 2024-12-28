<?php
require_once 'db.php'; // Deine DB-Verbindungsdatei

// Beispiel für die Berechtigung des Benutzers
session_start();

// Funktion, die überprüft, ob der Benutzer eine bestimmte Berechtigung hat
function has_permission($permission) {
    return isset($_SESSION['permissions'][$permission]) && $_SESSION['permissions'][$permission];
}

try {
    // SQL-Abfrage, um alle Ausbildungstypen abzurufen
    $sql = "SELECT id, key_name, display_name, description FROM ausbildungstypen";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $ausbildungstypen = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Füge Berechtigungen hinzu
    foreach ($ausbildungstypen as &$ausbildung) {
        $ausbildung['can_edit'] = has_permission('ausbildungstyp_update');
        $ausbildung['can_delete'] = has_permission('ausbildungstyp_remove');
    }

    // Header setzen, um die Antwort als JSON zurückzugeben
    header('Content-Type: application/json');
    echo json_encode($ausbildungstypen);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
}

$conn = null; // Verbindung schließen
?>
