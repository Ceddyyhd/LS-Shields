<?php
// Datenbankverbindung einbinden
include 'db.php';
session_start();

// Überprüfen, ob der Benutzer eingeloggt ist
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Benutzer ist nicht eingeloggt.']);
    exit;
}

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Ungültiges CSRF-Token']);
    exit;
}

// Überprüfen, ob alle benötigten Felder gesendet wurden
if (isset($_POST['unternehmen_name'], $_POST['ansprechperson_name'], $_POST['ansprechperson_nummer'], $_POST['adresse'], $_POST['unternehmen_art'])) {

    // Werte aus dem Formular holen
    $unternehmen_name = $_POST['unternehmen_name'];
    $ansprechperson_name = $_POST['ansprechperson_name'];
    $ansprechperson_nummer = $_POST['ansprechperson_nummer'];
    $adresse = $_POST['adresse'];
    $unternehmen_art = $_POST['unternehmen_art'];

    // SQL-Query zum Einfügen der Daten
    $query = "INSERT INTO kunden (unternehmen_name, ansprechperson_name, ansprechperson_nummer, adresse, unternehmen_art) 
              VALUES (:unternehmen_name, :ansprechperson_name, :ansprechperson_nummer, :adresse, :unternehmen_art)";
    $stmt = $conn->prepare($query);

    // Parameter binden
    $stmt->bindParam(':unternehmen_name', $unternehmen_name);
    $stmt->bindParam(':ansprechperson_name', $ansprechperson_name);
    $stmt->bindParam(':ansprechperson_nummer', $ansprechperson_nummer);
    $stmt->bindParam(':adresse', $adresse);
    $stmt->bindParam(':unternehmen_art', $unternehmen_art);

    // Ausführen und überprüfen, ob die Anfrage erfolgreich war
    if ($stmt->execute()) {
        // Log-Eintrag für die Erstellung des Kunden
        logAction('CREATE', 'kunden', 'Kunde erstellt: Unternehmen: ' . $unternehmen_name . ', erstellt von: ' . $_SESSION['user_id']);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Erstellen des Kunden']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Alle Felder sind erforderlich']);
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
