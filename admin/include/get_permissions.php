<?php
include 'security_check.php'; // Sicherheitsprüfung für diese Datei

include 'db.php';
header('Content-Type: application/json');

try {
    $stmt = $conn->prepare("SELECT * FROM permissions");
    $stmt->execute();
    $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'permissions' => $permissions]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
