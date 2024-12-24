<?php
include 'db.php'; // Datenbankverbindung

// Überprüfe, ob die Anfrage via POST gesendet wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Holen der Formulardaten
    $name = $_POST['name'] ?? '';
    $nummer = $_POST['nummer'] ?? '';
    $anfrage = $_POST['anfrage'] ?? '';
    $status = $_POST['status'] ?? 'Eingetroffen';
    $erstellt_von = $_POST['erstellt_von'] ?? 'Admin';  // Hier kannst du den Ersteller dynamisch setzen

    // Validierung der Eingabedaten
    if (empty($name) || empty($nummer) || empty($anfrage)) {
        echo json_encode(['success' => false, 'message' => 'Alle Felder müssen ausgefüllt werden!']);
        exit;
    }

    // Datum und Uhrzeit für die Anfrage
    $datum_uhrzeit = date('Y-m-d H:i:s');  // Aktuelles Datum und Uhrzeit

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
            ':erstellt_von' => $erstellt_von,
        ]);

        // Erfolgreiche Antwort zurückgeben
        echo json_encode(['success' => true, 'message' => 'Anfrage wurde erfolgreich erstellt.']);
    } catch (PDOException $e) {
        // Fehler bei der Datenbankoperation
        echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
    }
}
?>
