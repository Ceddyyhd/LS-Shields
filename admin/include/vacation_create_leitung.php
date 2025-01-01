<?php
include 'db.php';  // Datenbankverbindung einbinden

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Werte aus dem POST holen
    $user_id = $_POST['user_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];
    $note = $_POST['note'];  // Notiz
    $created_by = $_SESSION['username'];  // Wer hat den Urlaub erstellt?

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

        // Erfolgsantwort zurückgeben
        echo json_encode(['success' => true, 'id' => $vacation_id]);

    } catch (PDOException $e) {
        // Fehlerbehandlung
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
