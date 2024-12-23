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
        $logData = [];

        foreach ($ausbildungen as $key => $data) {
            $status = isset($data['status']) ? 1 : 0;
            $rating = isset($data['rating']) ? (int)$data['rating'] : 0;

            // Existierende Einträge prüfen
            $stmt = $conn->prepare("SELECT COUNT(*) FROM ausbildungen WHERE user_id = :user_id AND ausbildung = :key");
            $stmt->execute([':user_id' => $user_id, ':key' => $key]);
            $exists = $stmt->fetchColumn();

            if ($exists) {
                // Update Eintrag
                $stmt = $conn->prepare("UPDATE ausbildungen 
                                        SET status = :status, bewertung = :rating 
                                        WHERE user_id = :user_id AND ausbildung = :key");
                $stmt->execute([
                    ':status' => $status,
                    ':rating' => $rating,
                    ':user_id' => $user_id,
                    ':key' => $key,
                ]);
            } else {
                // Neuer Eintrag
                $stmt = $conn->prepare("INSERT INTO ausbildungen (user_id, ausbildung, status, bewertung) 
                                        VALUES (:user_id, :key, :status, :rating)");
                $stmt->execute([
                    ':user_id' => $user_id,
                    ':key' => $key,
                    ':status' => $status,
                    ':rating' => $rating,
                ]);
            }

            // Log-Daten vorbereiten
            $logData[] = [
                'user_id' => $user_id,
                'editor_name' => $_SESSION['username'] ?? 'Unbekannt',
                'ausbildung' => $key,
                'action' => $status ? 'hinzugefügt' : 'entfernt',
                'rating' => $rating,
            ];
        }

        // Logs speichern
        foreach ($logData as $log) {
            $stmt = $conn->prepare("INSERT INTO ausbildung_logs (user_id, editor_name, ausbildung, action, rating) 
                                    VALUES (:user_id, :editor_name, :ausbildung, :action, :rating)");
            $stmt->execute($log);
        }

        echo json_encode(['success' => true, 'message' => 'Änderungen erfolgreich gespeichert.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern: ' . $e->getMessage()]);
    }
    exit;
}
?>
