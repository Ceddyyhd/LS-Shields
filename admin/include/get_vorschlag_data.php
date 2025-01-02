<?php
include 'db.php'; // Datenbankverbindung

// Überprüfen, ob die Vorschlag-ID übergeben wurde
if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Keine Vorschlag-ID angegeben.']);
    exit;
}

$vorschlag_id = (int)$_GET['id'];

// Vorschlagsdaten aus der Datenbank holen
$stmt = $conn->prepare("SELECT vorschlag, betreff, status, notiz FROM verbesserungsvorschlaege WHERE id = :id");
$stmt->bindParam(':id', $vorschlag_id, PDO::PARAM_INT);
$stmt->execute();
$vorschlag = $stmt->fetch(PDO::FETCH_ASSOC);

if ($vorschlag) {
    echo json_encode(['success' => true, 'vorschlag' => $vorschlag]);
} else {
    echo json_encode(['success' => false, 'message' => 'Vorschlag nicht gefunden.']);
}
?>
