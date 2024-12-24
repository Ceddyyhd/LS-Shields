<?php
require_once 'include/db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



// Mitarbeiterdaten sicher abrufen
try {
    $stmt = $conn->prepare("
        SELECT 
            u.id,
            u.name,
            u.nummer,
            u.created_at,
            r.name AS role_name,
            (
                SELECT CONCAT(DATE_FORMAT(v.start_date, '%d.%m.%Y'), ' - ', DATE_FORMAT(v.end_date, '%d.%m.%Y'))
                FROM vacations v
                WHERE v.user_id = u.id AND v.status = 'approved' AND v.start_date >= CURDATE()
                ORDER BY v.start_date ASC
                LIMIT 1
            ) AS next_vacation
        FROM users u
        LEFT JOIN roles r ON u.role_id = r.id
    ");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debug-Ausgabe
    header('Content-Type: application/json');
    echo json_encode($users, JSON_PRETTY_PRINT);
    exit;

} catch (PDOException $e) {
    echo json_encode(['error' => 'Fehler beim Abrufen der Daten']);
}
?>
