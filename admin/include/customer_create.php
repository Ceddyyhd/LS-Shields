<?php
include 'security_check.php'; // Sicherheitsprüfung für diese Datei

require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage']);
    exit;
}

try {
    session_start();

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

    // Benutzer in die Datenbank einfügen
    $stmt = $conn->prepare("
        INSERT INTO kunden (umail, name, nummer, kontonummer, password, profile_image, created_at, remember_token)
        VALUES (?, ?, ?, ?, ?, ?, NOW(), NULL)
    ");
    $stmt->execute([$umail, $name, $nummer, $kontonummer, $passwordHash, $profileImagePath]);

    // ID des erstellten Benutzers abrufen
    $newUserId = $conn->lastInsertId();

    // Logging: Wer hat diesen Benutzer erstellt?
    $createdById = $_SESSION['user_id'] ?? null; // ID des aktuellen Benutzers aus der Session
    $createdByName = $_SESSION['username'] ?? 'Unbekannt'; // Name des aktuellen Benutzers aus der Session

    if ($createdById) {
        $logStmt = $conn->prepare("
            INSERT INTO kunden_logs (created_by, created_by_name, action, target_user)
            VALUES (?, ?, 'Kunde erstellt', ?)
        ");
        $logStmt->execute([$createdById, $createdByName, $newUserId]);
    }

    echo json_encode(['success' => true, 'message' => 'Benutzer erfolgreich erstellt.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
