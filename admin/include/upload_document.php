<?php
// Datenbankverbindung einbinden
include 'db.php';

// Überprüfen, ob das Formular abgeschickt wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prüfen, ob eine Datei hochgeladen wurde
    if (isset($_FILES) && !empty($_FILES)) {
        $user_id = $_POST['user_id'] ?? null; // Benutzer-ID aus dem Formular
        if (!$user_id) {
            die("Benutzer-ID fehlt.");
        }

        // Erlaubte Dateitypen
        $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
        $upload_dir = 'uploads/'; // Verzeichnis für hochgeladene Dateien

        // Datei-Handling
        foreach ($_FILES as $file_key => $file) {
            if ($file['error'] === UPLOAD_ERR_OK) {
                // Originalname und Dateiendung holen
                $file_name = basename($file['name']);
                $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

                // Prüfen, ob die Dateiendung erlaubt ist
                if (!in_array(strtolower($file_extension), $allowed_extensions)) {
                    echo "Ungültiger Dateityp für $file_name.";
                    continue;
                }

                // Eindeutigen Namen für die Datei erstellen
                $unique_name = uniqid('doc_', true) . '.' . $file_extension;
                $file_path = $upload_dir . $unique_name;

                // Datei verschieben
                if (move_uploaded_file($file['tmp_name'], $file_path)) {
                    // Datei in die Datenbank einfügen
                    $sql = "INSERT INTO documents (user_id, file_name, file_path, uploaded_at) 
                            VALUES (:user_id, :file_name, :file_path, NOW())";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([
                        'user_id' => $user_id,
                        'file_name' => $file_name,
                        'file_path' => $file_path
                    ]);
                } else {
                    echo "Fehler beim Hochladen der Datei $file_name.";
                }
            } else {
                echo "Fehler beim Hochladen der Datei: " . $file['name'];
            }
        }
    } else {
        echo "Keine Datei ausgewählt.";
    }
}

// Nach dem Upload zurück zur Profilseite
header("Location: profile.php?id=" . htmlspecialchars($_POST['user_id']));
exit;
