<?php
require_once 'db.php';

// Fehleranzeige für Debugging aktivieren
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'error' => 'Ungültiges CSRF-Token']);
    exit;
}

try {
    // Dein SQL-Statement für die Benutzerabfrage, jetzt nach dem 'value' der Rolle sortiert
    $stmt = $conn->prepare("
        SELECT 
            u.id,
            u.name,
            u.nummer,
            u.created_at,
            r.name AS role_name,
            r.value AS role_value,  -- Füge den 'value' der Rolle hinzu
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
        WHERE u.gekuendigt = 'no_kuendigung'
        AND u.bewerber = 'nein'
        ORDER BY r.value DESC;  -- Sortiere nach 'value' der Rolle in absteigender Reihenfolge
    ");

    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Wenn keine Benutzer gefunden wurden, gib ein leeres Array zurück
    if (empty($users)) {
        echo json_encode([]);
    } else {
        echo json_encode($users, JSON_PRETTY_PRINT);
    }
    exit;

} catch (PDOException $e) {
    // Fehlerausgabe bei SQL-Problemen
    error_log('SQL-Fehler: ' . $e->getMessage());
    echo json_encode(['error' => 'SQL-Fehler: ' . $e->getMessage()]);
    exit;
}
?>
