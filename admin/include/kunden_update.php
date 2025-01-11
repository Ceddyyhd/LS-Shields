<?php
include('db.php');
session_start();
header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['status' => 'error', 'message' => 'Ungültiges CSRF-Token']);
    exit;
}

// Kunden-ID und neuer Status (geloescht) aus dem Formular erhalten
$kundenId = $_POST['kunden_id'];  // Kunden-ID
$status = 'geloescht';  // Markiere den Kunden als gelöscht

// SQL-Befehl zum Aktualisieren des Kundenstatus
$stmt = $conn->prepare("UPDATE Kunden SET geloescht = ? WHERE id = ?");
$stmt->execute([$status, $kundenId]);

// Log-Eintrag für das Aktualisieren des Kundenstatus
logAction('UPDATE', 'Kunden', 'Kunde ID: ' . $kundenId . ' als gelöscht markiert von: ' . $_SESSION['user_id']);

echo json_encode(['status' => 'success', 'message' => 'Kunde wurde als gelöscht markiert.']);

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
