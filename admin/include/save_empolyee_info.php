<?php
include 'db.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $waffenschein_type = $_POST['waffenschein_type'] ?? 'none';
    $fuehrerscheine = json_encode($_POST['fuehrerscheine'] ?? []);

    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'Benutzer-ID fehlt.']);
        exit;
    }

    try {
        // Prüfen, ob ein Eintrag für den Mitarbeiter existiert
        $stmt = $conn->prepare("SELECT id FROM employees WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        $exists = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($exists) {
            // Wenn Eintrag existiert, aktualisieren
            $sql = "UPDATE employees SET waffenschein_type = :waffenschein_type, fuehrerscheine = :fuehrerscheine WHERE user_id = :user_id";
        } else {
            // Wenn kein Eintrag existiert, neuen erstellen
            $sql = "INSERT INTO employees (user_id, waffenschein_type, fuehrerscheine) VALUES (:user_id, :waffenschein_type, :fuehrerscheine)";
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':waffenschein_type' => $waffenschein_type,
            ':fuehrerscheine' => $fuehrerscheine
        ]);

        echo json_encode(['success' => true, 'message' => 'Daten erfolgreich gespeichert.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage.']);
}
