<?php
include 'db.php'; // Deine DB-Verbindung

if (isset($_GET['id'])) {
    $vorschlagId = $_GET['id'];

    // SQL-Abfrage, um den Vorschlag zu laden
    $stmt = $conn->prepare("SELECT * FROM verbesserungsvorschlaege WHERE id = :id");
    $stmt->bindParam(':id', $vorschlagId);
    $stmt->execute();
    $vorschlag = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($vorschlag) {
        // Erfolgreiche Antwort mit den Vorschlag-Daten
        echo json_encode([
            'success' => true,
            'vorschlag' => $vorschlag
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Vorschlag nicht gefunden'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Ungültige Anfrage'
    ]);
}
?>
