<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $ausbildungen = $_POST['ausbildungen'] ?? [];

    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'Benutzer-ID fehlt.']);
        exit;
    }

    try {
        foreach ($ausbildungen as $id => $data) {
            $status = $data['status'] ?? 0;
            $rating = $data['rating'] ?? 0;

            // Überprüfen, ob die Ausbildung existiert
            $stmt = $conn->prepare("SELECT COUNT(*) FROM ausbildungen WHERE user_id = :user_id AND ausbildung = :ausbildung");
            $stmt->execute([':user_id' => $user_id, ':ausbildung' => $id]);
            $exists = $stmt->fetchColumn();

            if ($exists) {
                // Aktualisieren
                $stmt = $conn->prepare("UPDATE ausbildungen SET status = :status, bewertung = :rating WHERE user_id = :user_id AND ausbildung = :ausbildung");
            } else {
                // Einfügen
                $stmt = $conn->prepare("INSERT INTO ausbildungen (user_id, ausbildung, status, bewertung) VALUES (:user_id, :ausbildung, :status, :rating)");
            }

            $stmt->execute([
                ':user_id' => $user_id,
                ':ausbildung' => $id,
                ':status' => $status,
                ':rating' => $rating,
            ]);
        }

        echo json_encode(['success' => true, 'message' => 'Änderungen erfolgreich gespeichert.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage.']);
}
