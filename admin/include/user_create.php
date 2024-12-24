<?php
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage']);
    exit;
}

try {
    // E-Mail und Passwort prüfen
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;

    if (!$email || !$password) {
        echo json_encode(['success' => false, 'message' => 'Bitte füllen Sie alle erforderlichen Felder aus.']);
        exit;
    }

    // Passwort-Hash erstellen
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Standardprofilbild, wenn kein Bild hochgeladen wird
    $profileImagePath = 'uploads/profile_images/standard.png';
    if (!empty($_FILES['profile_image']['name'])) {
        $uploadDir = 'uploads/profile_images/';
        $fileName = time() . '_' . basename($_FILES['profile_image']['name']);
        $targetFilePath = $uploadDir . $fileName;

        // Verzeichnis erstellen, falls nicht vorhanden
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFilePath)) {
            $profileImagePath = $targetFilePath;
        } else {
            echo json_encode(['success' => false, 'message' => 'Das Hochladen des Profilbilds ist fehlgeschlagen.']);
            exit;
        }
    }

    // Optional: Andere Felder (Name, Umail, etc.)
    $name = $_POST['name'] ?? null;
    $umail = $_POST['umail'] ?? null;
    $kontonummer = $_POST['kontonummer'] ?? null;
    $nummer = $_POST['nummer'] ?? null;
    $role_id = $_POST['role_id'] ?? null;

    // Benutzer in die Datenbank einfügen
    $stmt = $conn->prepare("
        INSERT INTO users (email, umail, name, nummer, kontonummer, password, role_id, created_at, remember_token, rank_last_changed_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NULL, NULL)
    ");
    $stmt->execute([$email, $umail, $name, $nummer, $kontonummer, $passwordHash, $role_id]);

    echo json_encode(['success' => true, 'message' => 'Benutzer erfolgreich erstellt.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
