<?php
include 'db.php'; // Datenbankverbindung

session_start(); // Ensure session is started
header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Ungültiges CSRF-Token']);
    exit;
}

if (isset($_POST['id'])) {
    // Einzelnen Ausbildungstyp abrufen
    $id = $_POST['id'];
    try {
        $stmt = $conn->prepare("SELECT * FROM ausbildungstypen WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $ausbildung = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($ausbildung) {
            echo json_encode(['success' => true, 'ausbildung' => $ausbildung]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Ausbildungstyp nicht gefunden.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
    }
} else {
    // Alle Ausbildungstypen abrufen
    try {
        $stmt = $conn->prepare("SELECT * FROM ausbildungstypen");
        $stmt->execute();
        $ausbildungstypen = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($ausbildungstypen) {
            echo json_encode(['success' => true, 'ausbildungstypen' => $ausbildungstypen]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Keine Ausbildungstypen gefunden.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
    }
}

// Verbindung schließen
$conn = null;
?>