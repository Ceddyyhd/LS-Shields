<?php
include 'db.php'; // Datenbankverbindung
session_start();

// Überprüfen, ob der Benutzer eingeloggt ist
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Benutzer ist nicht eingeloggt.']);
    exit;
}

// Formulardaten auslesen
$vorschlag_id = $_POST['id'] ?? '';
$zustimmung = $_POST['zustimmung'] ?? false; // TRUE = Zustimmung, FALSE = Ablehnung
$user_id = $_SESSION['user_id']; // Die Benutzer-ID aus der Session

// Überprüfen, ob die Eingaben korrekt sind
if (!$vorschlag_id || !isset($zustimmung)) {
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage']);
    exit;
}

try {
    // Prüfen, ob der Benutzer den Vorschlag bereits bewertet hat
    $stmt = $conn->prepare("SELECT * FROM vorschlag_zustimmungen WHERE vorschlag_id = :vorschlag_id AND user_id = :user_id");
    $stmt->execute([':vorschlag_id' => $vorschlag_id, ':user_id' => $user_id]);
    $existingRating = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingRating) {
        echo json_encode(['success' => false, 'message' => 'Sie haben diesen Vorschlag bereits bewertet.']);
        exit;
    }

    // Eintrag für Zustimmung oder Ablehnung in die Tabelle vorschlag_zustimmungen
    $stmt = $conn->prepare("INSERT INTO vorschlag_zustimmungen (vorschlag_id, user_id, zustimmung) VALUES (:vorschlag_id, :user_id, :zustimmung)");
    $stmt->execute([':vorschlag_id' => $vorschlag_id, ':user_id' => $user_id, ':zustimmung' => $zustimmung]);

    // Aktualisieren der Zustimmungen/Ablehnungen in der Haupttabelle
    if ($zustimmung) {
        $stmt = $conn->prepare("UPDATE verbesserungsvorschlaege SET zustimmungen = zustimmungen + 1 WHERE id = :id");
    } else {
        $stmt = $conn->prepare("UPDATE verbesserungsvorschlaege SET ablehnungen = ablehnungen + 1 WHERE id = :id");
    }
    $stmt->execute([':id' => $vorschlag_id]);

    // Holen der neuen Zustimmungs-/Ablehnungszahlen
    $stmt = $conn->prepare("SELECT zustimmungen, ablehnungen FROM verbesserungsvorschlaege WHERE id = :id");
    $stmt->execute([':id' => $vorschlag_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'zustimmungen' => $result['zustimmungen'], 'ablehnungen' => $result['ablehnungen']]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>
