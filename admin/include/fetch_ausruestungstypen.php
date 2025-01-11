<?php
include 'db.php'; // Datenbankverbindung

session_start(); // Ensure session is started
header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Ungültiges CSRF-Token']);
    exit;
}

// Funktion, die überprüft, ob der Benutzer eine bestimmte Berechtigung hat
function has_permission($permission) {
    return isset($_SESSION['permissions'][$permission]) && $_SESSION['permissions'][$permission];
}

if (isset($_POST['id'])) {
    // Einzelne Ausruestung abrufen
    $id = $_POST['id'];
    try {
        $stmt = $conn->prepare("SELECT * FROM ausruestungstypen WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $ausruestung = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($ausruestung) {
            echo json_encode(['success' => true, 'ausruestung' => $ausruestung]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Ausruestung nicht gefunden.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
    }
} else {
    // Alle Ausruestung abrufen
    try {
        $stmt = $conn->prepare("SELECT * FROM ausruestungstypen");
        $stmt->execute();
        $ausruestung = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($ausruestung) {
            echo json_encode(['success' => true, 'ausruestung' => $ausruestung]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Keine Ausruestung gefunden.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
    }
}

// Verbindung schließen
$conn = null;
?>
