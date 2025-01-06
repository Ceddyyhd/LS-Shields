<?php
include 'db.php';

if (isset($_GET['id'])) {
    $ausruestungId = $_GET['id'];

    try {
        $stmt = $conn->prepare("SELECT * FROM ausruestung_history WHERE key_name = :key_name ORDER BY timestamp DESC");
        $stmt->execute([':key_name' => $ausruestungId]);
        $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($history);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Abrufen der Historie: ' . $e->getMessage()]);
    }
}
?>
