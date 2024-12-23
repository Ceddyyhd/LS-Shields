<?php
// Datenbankverbindung einbinden
include 'db.php';

// Debugging: POST- und FILES-Daten ausgeben (optional)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<pre>";
    print_r($_POST);
    print_r($_FILES);
    echo "</pre>";
    // Entferne das `die()` nach dem Debugging!
}

// Überprüfen, ob das Formular abgeschickt wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Benutzer-ID und benutzerdefinierter Name überprüfen
    $user_id = $_POST['user_id'] ?? null;
    $custom_name = $_POST['document_name'] ?? null; // Achte darauf, dass der Name übereinstimmt
    $doc_type = $_POST['doc_type'] ?? 'unbekannt';

    if (!$user_id || !$custom_name) {
        die("Benutzer-ID oder Dokumentname fehlt.");
    }

    // Verzeichnis für hochgeladene Dateien
    $upload_dir = '../uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Erlaubte Dateitypen
    $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];

    // Datei verarbeiten
    if (!empty($_FILES['document_file']['name'])) {
        $file = $_FILES['document_file'];
        $file_name = basename($file['name']);
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

        // Dateityp überprüfen
        if (!in_array(strtolower($file_extension), $allowed_extensions)) {
            die("Ungültiger Dateityp.");
        }

        // Eindeutigen Dateinamen erstellen
        $unique_name = $custom_name . '_' . uniqid('doc_', true) . '.' . $file_extension;
        $physical_path = $upload_dir . $unique_name;
        $file_path = '/admin/uploads/' . $unique_name; // Pfad für die Datenbank

        // Datei verschieben
        if (move_uploaded_file($file['tmp_name'], $physical_path)) {
            // In Datenbank speichern
            $sql = "INSERT INTO documents (user_id, file_name, file_path, uploaded_at, doc_type) 
                    VALUES (:user_id, :file_name, :file_path, NOW(), :doc_type)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'user_id' => $user_id,
                'file_name' => $custom_name,
                'file_path' => $file_path,
                'doc_type' => $doc_type
            ]);

            // Erfolgsnachricht
            echo "<script>alert('Datei erfolgreich hochgeladen!'); window.location.href='../profile.php?id=" . htmlspecialchars($user_id) . "';</script>";
        } else {
            die("Fehler beim Hochladen der Datei.");
        }
    } else {
        die("Keine Datei ausgewählt.");
    }
} else {
    die("Ungültige Anforderung.");
}
?>
