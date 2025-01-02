<?php
include 'db.php'; // Datenbankverbindung

// Überprüfen, ob die ID und die anderen Daten übergeben wurden
if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'Keine ID angegeben.']);
    exit;
}

$id = (int)$_POST['id'];
$status = $_POST['status'] ?? '';
$notiz = $_POST['notiz'] ?? '';

// SQL-Update für den Vorschlag
try {
    $stmt = $conn->prepare("UPDATE verbesserungsvorschlaege SET status = :status WHERE id = :id");
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // SQL-Update für die Bearbeitung in der neuen Tabelle
    $stmt = $conn->prepare("INSERT INTO verbesserungsvorschlag_bearbeitung (vorschlag_id, status, notiz, bearbeitet_von)
                            VALUES (:vorschlag_id, :status, :notiz, :bearbeitet_von)");
    $stmt->execute([
        ':vorschlag_id' => $id,
        ':status' => $status,
        ':notiz' => $notiz,
        ':bearbeitet_von' => $_SESSION['username'],  // Aktuellen Benutzernamen verwenden
    ]);

    echo json_encode(['success' => true, 'message' => 'Vorschlag erfolgreich bearbeitet.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
}
?>
