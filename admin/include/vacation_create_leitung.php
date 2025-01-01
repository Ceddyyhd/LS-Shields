<?php
include 'db.php'; // Datenbankverbindung einbinden
session_start(); // Session starten, um auf die Benutzerdaten zuzugreifen

// Prüfen, ob das Formular über POST gesendet wurde
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Benutzereingaben aus dem Formular holen
    $user_id = $_POST['user_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status']; // Status kann "pending", "approved" oder "rejected" sein
    $note = $_POST['note'];
    $erstellt_von = $_SESSION['username']; // Benutzername des Erstellers

    try {
        // SQL-Abfrage, um den neuen Urlaubsantrag in die Datenbank einzufügen
        $sql = "INSERT INTO vacations (user_id, start_date, end_date, status, note, erstellt_von) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_id, $start_date, $end_date, $status, $note, $erstellt_von]);

        // Erfolgsantwort zurückgeben
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        // Fehlerbehandlung bei der Datenbankabfrage
        echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
    }
}
?>
