<?php
include 'db.php'; // Datenbankverbindung

session_start(); // Sitzung starten

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vorschlagId = (int) $_POST['id']; // Vorschlag ID
    $zustimmung = ($_POST['zustimmung'] === 'true') ? 1 : 0;  // Umwandeln von 'true'/'false' in 1/0
    $userId = $_SESSION['user_id'];  // Benutzer-ID aus der Session holen

    // Überprüfen, ob der Benutzer bereits abgestimmt hat
    $checkStmt = $conn->prepare("SELECT * FROM vorschlag_zustimmungen WHERE vorschlag_id = :vorschlag_id AND user_id = :user_id");
    $checkStmt->execute([
        ':vorschlag_id' => $vorschlagId,
        ':user_id' => $userId
    ]);
    $existingVote = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($existingVote) {
        // Wenn der Benutzer bereits abgestimmt hat, Fehlermeldung zurückgeben
        echo json_encode(['success' => false, 'message' => 'Sie haben bereits abgestimmt.']);
        exit;
    }

    // Speichern der Zustimmung/Ablehnung
    $stmt = $conn->prepare("INSERT INTO vorschlag_zustimmungen (vorschlag_id, user_id, zustimmung) VALUES (:vorschlag_id, :user_id, :zustimmung)");
    $stmt->execute([
        ':vorschlag_id' => $vorschlagId,
        ':user_id' => $userId,
        ':zustimmung' => $zustimmung
    ]);

    // Aktualisieren der Zähler in der `verbesserungsvorschlaege`-Tabelle
    if ($zustimmung === 1) {
        $updateStmt = $conn->prepare("UPDATE verbesserungsvorschlaege SET zustimmungen = zustimmungen + 1 WHERE id = :id");
    } else {
        $updateStmt = $conn->prepare("UPDATE verbesserungsvorschlaege SET ablehnungen = ablehnungen + 1 WHERE id = :id");
    }

    $updateStmt->execute([':id' => $vorschlagId]);

    // Zähler abrufen
    $stmt = $conn->prepare("SELECT zustimmungen, ablehnungen FROM verbesserungsvorschlaege WHERE id = :id");
    $stmt->execute([':id' => $vorschlagId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Rückgabe der neuen Werte für Zustimmungen und Ablehnungen
    echo json_encode([
        'success' => true,
        'zustimmungen' => (int) $result['zustimmungen'],  // Stellen sicher, dass es eine Zahl ist
        'ablehnungen' => (int) $result['ablehnungen']
    ]);
}
?>