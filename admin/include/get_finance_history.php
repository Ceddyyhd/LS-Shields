<?php
include 'security_check.php'; // Sicherheitsprüfung für diese Datei

include 'db.php';

// Überprüfen, ob die user_id übergeben wurde
if (isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];

    // SQL-Abfrage für Historie-Daten
    $stmt = $conn->prepare("SELECT * FROM finanzen_history WHERE user_id = ?");
    $stmt->execute([$userId]);
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($history) {
        echo json_encode(['success' => true, 'history' => $history]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Keine Historie-Daten gefunden.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Keine user_id angegeben.']);
}
?>
