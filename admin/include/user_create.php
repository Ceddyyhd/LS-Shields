<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Empfange und validiere die Benutzerdaten
        $email = $_POST['email'] ?? null;
        $umail = $_POST['umail'] ?? null;
        $name = $_POST['name'] ?? null;
        $nummer = $_POST['nummer'] ?? null;
        $kontonummer = $_POST['kontonummer'] ?? null;
        $password = $_POST['password'] ?? null;
        $role_id = $_POST['role_id'] ?? null;

        if (!$email || !$umail || !$name || !$password) {
            throw new Exception('Bitte füllen Sie alle erforderlichen Felder aus.');
        }

        // Passwort-Hash erstellen
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Profilbild hochladen
        $profileImagePath = null;
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
                throw new Exception('Das Hochladen des Profilbilds ist fehlgeschlagen.');
            }
        }

        // Daten in die Datenbank einfügen
        $stmt = $conn->prepare("
            INSERT INTO users (email, umail, name, nummer, kontonummer, password, role_id, created_at, remember_token, rank_last_changed_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NULL, NULL)
        ");
        $stmt->execute([$email, $umail, $name, $nummer, $kontonummer, $passwordHash, $role_id]);

        // Erfolgsmeldung
        echo json_encode(['success' => true, 'message' => 'Benutzer erfolgreich erstellt.']);
    } catch (Exception $e) {
        // Fehlerbehandlung
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
