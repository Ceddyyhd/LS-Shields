<?php
include 'db.php';  // Datenbankverbindung einbinden
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Überprüfen, ob das CSRF-Token gültig ist
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false, 'message' => 'Ungültiges CSRF-Token']);
        exit;
    }

    // Werte aus dem POST holen
    $user_id = $_POST['user_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];
    $note = $_POST['note'];  // Notiz
    $created_by = $_POST['user_name'];  // Benutzername direkt aus dem Formular (hidden input)

    try {
        // SQL-Abfrage zum Einfügen des neuen Urlaubsantrags
        $sql_insert = "INSERT INTO vacations (user_id, start_date, end_date, status, note, erstellt_von) 
                        VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->execute([$user_id, $start_date, $end_date, $status, $note, $created_by]);

        $vacation_id = $conn->lastInsertId(); // ID des eingefügten Urlaubsantrags

        // Log-Eintrag erstellen
        $action = "Urlaubsantrag erstellt: Startdatum: $start_date, Enddatum: $end_date, Status: $status, Notiz: $note";
        $sql_log = "INSERT INTO vacations_logs (vacation_id, action, user_name) 
                    VALUES (?, ?, ?)";
        $stmt_log = $conn->prepare($sql_log);
        $stmt_log->execute([$vacation_id, $action, $created_by]);

        // Allgemeiner Log-Eintrag
        logAction('CREATE', 'vacations', 'Urlaubsantrag erstellt: ID: ' . $vacation_id . ', erstellt von: ' . $_SESSION['user_id']);

        // Erfolgsantwort zurückgeben
        echo json_encode(['success' => true, 'id' => $vacation_id]);

    } catch (PDOException $e) {
        // Fehlerbehandlung
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// Funktion zum Loggen von Aktionen
function logAction($action, $table, $details) {
    global $conn;

    // SQL-Abfrage zum Einfügen des Log-Eintrags
    $stmt = $conn->prepare("INSERT INTO logs (action, table_name, details, user_id, timestamp) VALUES (:action, :table_name, :details, :user_id, NOW())");
    $stmt->bindParam(':action', $action, PDO::PARAM_STR);
    $stmt->bindParam(':table_name', $table, PDO::PARAM_STR);
    $stmt->bindParam(':details', $details, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
}
?>
