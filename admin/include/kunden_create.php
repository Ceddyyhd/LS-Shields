<?php
include('db.php');
session_start();
header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['status' => 'error', 'message' => 'Ungültiges CSRF-Token']);
    exit;
}

// Überprüfen, ob die Daten über POST gesendet wurden
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $umail = $_POST['umail'];
    $name = $_POST['name'];
    $nummer = $_POST['nummer'];
    $kontonummer = $_POST['kontonummer'];

    // SQL-Abfrage, um den neuen Kunden hinzuzufügen
    $stmt = $conn->prepare("INSERT INTO Kunden (umail, name, nummer, kontonummer) VALUES (?, ?, ?, ?)");
    $stmt->execute([$umail, $name, $nummer, $kontonummer]);

    // Log-Eintrag für das Erstellen des Kunden
    logAction('INSERT', 'Kunden', 'Kunde erstellt: ' . $name . ', erstellt von: ' . $_SESSION['user_id']);

    echo json_encode(['status' => 'success', 'message' => 'Kunde erfolgreich erstellt!']);
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
