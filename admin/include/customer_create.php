<?php
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage']);
    exit;
}

try {
    session_start();

    // Überprüfen, ob das CSRF-Token gültig ist
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header('Location: ../error.php');
        exit;
    }

    // Pflichtfelder prüfen
    $password = $_POST['password'] ?? null;

    if (!$password) {
        echo json_encode(['success' => false, 'message' => 'Bitte füllen Sie alle erforderlichen Felder aus.']);
        exit;
    }

    // Passwort-Hash erstellen
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Upload-Verzeichnis anpassen
    $uploadDir = __DIR__ . '/../uploads/profile_images/';
    $profileImagePath = 'uploads/profile_images/standard.png'; // Standardbildpfad

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $fileName = time() . '_' . basename($_FILES['profile_image']['name']);
        $targetFilePath = $uploadDir . $fileName;

        // Verzeichnis erstellen, falls nicht vorhanden
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Datei verschieben und Pfad speichern
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFilePath)) {
            $profileImagePath = 'uploads/profile_images/' . $fileName; // Relativer Pfad
        }
    }

    // Optionale Felder
    $name = $_POST['name'] ?? null;
    $umail = $_POST['umail'] ?? null;
    $kontonummer = $_POST['kontonummer'] ?? null;
    $nummer = $_POST['nummer'] ?? null;

    // Benutzer in der Datenbank speichern
    $stmt = $conn->prepare("
        INSERT INTO customers (name, umail, kontonummer, nummer, password, profile_image) 
        VALUES (:name, :umail, :kontonummer, :nummer, :password, :profile_image)
    ");
    $stmt->execute([
        ':name' => $name,
        ':umail' => $umail,
        ':kontonummer' => $kontonummer,
        ':nummer' => $nummer,
        ':password' => $passwordHash,
        ':profile_image' => $profileImagePath
    ]);

    // Log-Eintrag für das Erstellen des Kunden
    $customer_id = $conn->lastInsertId();
    logAction('INSERT', 'customers', 'customer_id: ' . $customer_id . ', created_by: ' . $_SESSION['user_id']);

    echo json_encode(['success' => true, 'message' => 'Kunde wurde erfolgreich erstellt.']);
} catch (PDOException $e) {
    error_log('Fehler beim Erstellen des Kunden: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Fehler beim Erstellen des Kunden: ' . $e->getMessage()]);
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
