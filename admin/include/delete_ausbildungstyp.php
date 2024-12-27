<?php
require_once 'db.php'; // Deine DB-Verbindungsdatei

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    try {
        // SQL-Abfrage zum Löschen eines Ausbildungstyps
        $sql = "DELETE FROM ausbildungstypen WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Fehler beim Löschen des Ausbildungstyps.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Keine ID angegeben.']);
}
?>
