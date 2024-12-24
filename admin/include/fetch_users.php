<?php
require_once 'db.php';

// Fehleranzeige für Debugging aktivieren
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // Mitarbeiterdaten sicher abrufen, nur Benutzer, deren kuendigung 'no_kuendigung' ist
    $stmt = $conn->prepare("
        SELECT 
            u.id,
            u.name,
            u.nummer,
            u.created_at,
            r.name AS role_name,
            CASE
                -- Prüfen, ob der Mitarbeiter aktuell im Urlaub ist
                WHEN EXISTS (
                    SELECT 1
                    FROM vacations v
                    WHERE v.user_id = u.id 
                    AND v.status = 'approved' 
                    AND CURDATE() BETWEEN v.start_date AND v.end_date
                ) THEN (
                    SELECT CONCAT('Im Urlaub: ', DATE_FORMAT(v.start_date, '%d.%m.%Y'), ' - ', DATE_FORMAT(v.end_date, '%d.%m.%Y'))
                    FROM vacations v
                    WHERE v.user_id = u.id 
                    AND v.status = 'approved' 
                    AND CURDATE() BETWEEN v.start_date AND v.end_date
                    LIMIT 1
                )
                -- Andernfalls den nächsten geplanten Urlaub anzeigen
                ELSE (
                    SELECT CONCAT(DATE_FORMAT(v.start_date, '%d.%m.%Y'), ' - ', DATE_FORMAT(v.end_date, '%d.%m.%Y'))
                    FROM vacations v
                    WHERE v.user_id = u.id 
                    AND v.status = 'approved' 
                    AND v.start_date >= CURDATE()
                    ORDER BY v.start_date ASC
                    LIMIT 1
                )
            END AS next_vacation
        FROM users u
        LEFT JOIN roles r ON u.role_id = r.id
        WHERE u.kuendigung = 'no_kuendigung';  -- Hier wird überprüft, dass die kuendigung 'no_kuendigung' ist
    ");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // JSON-Ausgabe der Daten
    header('Content-Type: application/json');
    echo json_encode($users, JSON_PRETTY_PRINT);
    exit;

} catch (PDOException $e) {
    // Fehlerausgabe bei SQL-Problemen
    header('Content-Type: application/json');
    echo json_encode(['error' => 'SQL-Fehler: ' . $e->getMessage()]);
    exit;
}
?>
