<?php
include 'security_check.php'; // Sicherheitsprüfung für diese Datei
include 'db.php'; // Datenbankverbindung

session_start(); // Sitzung starten

// Benutzernamen aus dem POST-Daten ziehen
$erstellt_von = $_POST['erstellt_von'] ?? '';

// Formulardaten auslesen
$name = $_POST['name'] ?? '';
$nummer = $_POST['nummer'] ?? '';
$anfrage = $_POST['anfrage'] ?? '';
$status = 'Eingetroffen'; // Standardstatus
$datum_uhrzeit = date('Y-m-d H:i:s'); // Aktuelles Datum und Uhrzeit

// Validierung der Formulardaten
if (empty($name) || empty($nummer) || empty($anfrage) || empty($erstellt_von)) {
    echo json_encode(['success' => false, 'message' => 'Alle Felder müssen ausgefüllt werden!']);
    exit;
}

try {
    // SQL zum Einfügen der Anfrage in die Datenbank
    $sql = "INSERT INTO anfragen (vorname_nachname, telefonnummer, anfrage, datum_uhrzeit, status, erstellt_von)
            VALUES (:name, :nummer, :anfrage, :datum_uhrzeit, :status, :erstellt_von)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':name' => $name,
        ':nummer' => $nummer,
        ':anfrage' => $anfrage,
        ':datum_uhrzeit' => $datum_uhrzeit,
        ':status' => $status,
        ':erstellt_von' => $erstellt_von, // Der Benutzername wird hier gespeichert
    ]);

    // Hole die ID der neu eingefügten Anfrage
    $anfrage_id = $conn->lastInsertId(); // Letzte eingefügte ID (Anfrage-ID)
    
    if ($user) {
        $user_id = $user['id'];

        // Log-Daten in der anfragen_logs-Tabelle speichern
        $action = "Anfrage erstellt";  // Beschreibung der Aktion
        $logSql = "INSERT INTO anfragen_logs (user_id, action, anfrage_id) VALUES (:user_id, :action, :anfrage_id)";
        $logStmt = $conn->prepare($logSql);
        $logStmt->execute([
            ':user_id' => $user_id,
            ':action' => $action,
            ':anfrage_id' => $anfrage_id // Verknüpfen mit der ID der neu erstellten Anfrage
        ]);
    }

    echo json_encode(['success' => true, 'message' => 'Anfrage wurde erfolgreich erstellt.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>
