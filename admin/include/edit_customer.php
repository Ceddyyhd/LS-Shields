<?php
// Verbindung und Sitzung starten
include 'db.php';
session_start();
header('Content-Type: application/json');

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Ungültiges CSRF-Token']);
    exit;
}

// Überprüfen, ob die Anfrage korrekt ist
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['user_id'] ?? null; // Die ID ist weiterhin als user_id im Formular
    if (!$customer_id) {
        echo json_encode(['success' => false, 'message' => 'Kunden-ID fehlt.']);
        exit;
    }

    // Berechtigungen überprüfen und Updates vorbereiten
    $updates = [];
    if ($_SESSION['permissions']['edit_name'] ?? false) {
        $updates['name'] = $_POST['name'] ?? '';
    }
    if ($_SESSION['permissions']['edit_nummer'] ?? false) {
        $updates['nummer'] = $_POST['nummer'] ?? '';
    }
    if ($_SESSION['permissions']['edit_umail'] ?? false) {
        $updates['umail'] = $_POST['umail'] ?? '';
    }
    if ($_SESSION['permissions']['edit_kontonummer'] ?? false) {
        $updates['kontonummer'] = $_POST['kontonummer'] ?? '';
    }

    // Passwort verarbeiten, falls erlaubt und übergeben
    if (isset($_POST['password']) && $_SESSION['permissions']['edit_password'] ?? false) {
        $password = $_POST['password'];

        // Überprüfen, ob das Passwort leer ist, obwohl die Checkbox aktiviert wurde
        if (empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Passwort darf nicht leer sein.']);
            exit;
        }

        // Passwort hashen und hinzufügen
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $updates['password'] = $hashedPassword;
    }

    // Gekündigt verarbeiten (hier stellen wir sicher, dass nur die gültigen Werte gespeichert werden)
    $gekuendigt = isset($_POST['gekuendigt']) && $_POST['gekuendigt'] === 'on' ? 'gekuendigt' : 'no_kuendigung';
    $updates['gekuendigt'] = $gekuendigt;  // Den "gekuendigt"-Wert in das Updates-Array einfügen

    // Daten aktualisieren
    try {
        $setPart = [];
        foreach ($updates as $column => $value) {
            $setPart[] = "$column = :$column";
        }
        $setPart = implode(', ', $setPart);

        $sql = "UPDATE customers SET $setPart WHERE id = :customer_id";
        $stmt = $conn->prepare($sql);
        foreach ($updates as $column => $value) {
            $stmt->bindValue(":$column", $value);
        }
        $stmt->bindValue(':customer_id', $customer_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Log-Eintrag für das Bearbeiten
            logAction('UPDATE', 'customers', 'customer_id: ' . $customer_id . ', updated_by: ' . $_SESSION['user_id']);

            echo json_encode(['success' => true, 'message' => 'Kundendaten erfolgreich aktualisiert.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Fehler beim Aktualisieren der Kundendaten.']);
        }
    } catch (PDOException $e) {
        error_log('Fehler beim Aktualisieren der Kundendaten: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Fehler beim Aktualisieren der Kundendaten: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage.']);
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
