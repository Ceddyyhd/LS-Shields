<?php
// Datenbankverbindung einbinden
include 'db.php';

// Überprüfen, ob das Formular abgeschickt wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Benutzer-ID überprüfen (muss im Formular enthalten sein)
    $user_id = $_POST['user_id'] ?? null;
    if (!$user_id) {
        die("Benutzer-ID fehlt.");
    }

    // Dokumenttyp überprüfen (muss im Formular enthalten sein)
    $doc_type = $_POST['doc_type'] ?? null;
    if (!$doc_type) {
        die("Dokumenttyp fehlt.");
    }

    // Verzeichnis für hochgeladene Dateien
    $upload_dir = '../uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Erlaubte Dateitypen
    $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];

    // Überprüfen, ob eine Datei hochgeladen wurde
    if (!empty($_FILES['file']['name'])) {
        $file = $_FILES['file'];

        if ($file['error'] === UPLOAD_ERR_OK) {
            $file_name = basename($file['name']);
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

            // Überprüfen, ob der Dateityp erlaubt ist
            if (!in_array(strtolower($file_extension), $allowed_extensions)) {
                die("Ungültiger Dateityp.");
            }

            // Eindeutigen Dateinamen erstellen
            $unique_name = $doc_type . '_' . $user_id . '_' . uniqid() . '.' . $file_extension;
            $physical_path = $upload_dir . $unique_name;
            $file_path = '/admin/uploads/' . $unique_name; // Für die Datenbank

            // Datei verschieben
            if (move_uploaded_file($file['tmp_name'], $physical_path)) {
                // In die Datenbank einfügen
                $sql = "INSERT INTO documents (user_id, file_name, file_path, uploaded_at, doc_type) 
                        VALUES (:user_id, :file_name, :file_path, NOW(), :doc_type)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    'user_id' => $user_id,
                    'file_name' => $file_name,
                    'file_path' => $file_path,
                    'doc_type' => $doc_type
                ]);

                // Erfolgsnachricht
                header("Location: ../profile.php?id=" . htmlspecialchars($user_id));
                exit;
            } else {
                die("Fehler beim Hochladen der Datei.");
            }
        } else {
            die("Fehler beim Hochladen der Datei: " . $file['error']);
        }
    } else {
        die("Keine Datei ausgewählt.");
    }
}

// Wenn kein POST-Request
header("Location: ../profile.php");
exit;