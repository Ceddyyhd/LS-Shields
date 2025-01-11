<?php
session_start();
include 'db.php'; // Deine Datenbankverbindung
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Überprüfen, ob das CSRF-Token gültig ist
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['status' => 'error', 'message' => 'Ungültiges CSRF-Token']);
        exit;
    }

    // Überprüfe, ob das unsichtbare Feld 'erstellt_von' gesetzt wurde
    if (isset($_POST['erstellt_von'])) {
        $erstellt_von = $_POST['erstellt_von'];
    } else {
        echo json_encode(["status" => "error", "message" => "Benutzer nicht angegeben."]);
        exit();
    }

    // Eingabewerte aus dem Formular holen
    $typ = $_POST['typ'];
    $kategorie = $_POST['kategorie'];
    $notiz = $_POST['notiz'];
    $betrag = $_POST['betrag'];

    // SQL-Query, um den neuen Eintrag hinzuzufügen
    $sql = "INSERT INTO finanzen (typ, kategorie, notiz, betrag, erstellt_von) 
            VALUES (:typ, :kategorie, :notiz, :betrag, :erstellt_von)";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':typ', $typ);
        $stmt->bindParam(':kategorie', $kategorie);
        $stmt->bindParam(':notiz', $notiz);
        $stmt->bindParam(':betrag', $betrag);
        $stmt->bindParam(':erstellt_von', $erstellt_von);

        // Query ausführen
        $stmt->execute();

        // Log-Eintrag für das Hinzufügen
        logAction('INSERT', 'finanzen', 'entry_id: ' . $conn->lastInsertId() . ', erstellt_von: ' . $erstellt_von);

        // Erfolgreiche Antwort zurückgeben
        echo json_encode(["status" => "success", "message" => "Eintrag erfolgreich hinzugefügt!"]);
    } catch (PDOException $e) {
        // Fehlerbehandlung
        error_log('Fehler beim Hinzufügen des Eintrags: ' . $e->getMessage());
        echo json_encode(["status" => "error", "message" => "Fehler: " . $e->getMessage()]);
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
