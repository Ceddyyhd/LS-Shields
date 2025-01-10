<?php
include 'db.php';  // Stellen Sie sicher, dass die Datenbankverbindung korrekt ist

// Funktion zum Generieren eines zufälligen Einladungscodes
function generateInviteCode($length = 10) {
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Generiere den Einladungscode
    $invite_code = generateInviteCode();

    // Optional: Ablaufdatum für den Code setzen (z.B. 30 Tage ab heute)
    $expired_at = date('Y-m-d H:i:s', strtotime('+30 days'));

    // Den Einladungscode in die Datenbank einfügen
    try {
        $stmt = $conn->prepare("INSERT INTO invites (invite_code, expired_at) VALUES (:invite_code, :expired_at)");
        $stmt->execute([
            ':invite_code' => $invite_code,
            ':expired_at' => $expired_at
        ]);

        // Die neu eingefügten Daten zurückgeben
        echo json_encode([
            'success' => true,
            'id' => $conn->lastInsertId(),
            'invite_code' => $invite_code,
            'created_at' => date('Y-m-d H:i:s'),
            'expired_at' => $expired_at
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
    }
}
