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

    // Verzeichnis für hochgeladene Dateien
    $upload_dir = '../uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Erlaubte Dateitypen
    $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];

    // Hochlade-Funktion für eine Datei
    function handleFileUpload($file, $upload_dir, $user_id, $doc_type, $conn)
    {
        global $allowed_extensions;

        if ($file['error'] === UPLOAD_ERR_OK) {
            $file_name = basename($file['name']);
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

            // Überprüfen, ob der Dateityp erlaubt ist
            if (!in_array(strtolower($file_extension), $allowed_extensions)) {
                echo "Ungültiger Dateityp für $file_name.";
                return;
            }
            $upload_dir_db = 'admin/uploads/';
            // Eindeutigen Dateinamen erstellen
            $unique_name = uniqid('doc_', true) . '.' . $file_extension;
            $file_path = $upload_dir_db . $unique_name;

            // Datei verschieben
            if (move_uploaded_file($file['tmp_name'], $file_path)) {
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
                echo "Datei $file_name erfolgreich hochgeladen.";
            } else {
                echo "Fehler beim Hochladen der Datei $file_name.";
            }
        } else {
            echo "Fehler beim Hochladen der Datei.";
        }
    }

    // Überprüfen und Verarbeiten der hochgeladenen Dateien
    if (!empty($_FILES['waffenschein_file']['name'])) {
        handleFileUpload($_FILES['waffenschein_file'], $upload_dir, $user_id, 'waffenschein', $conn);
    }
    if (!empty($_FILES['fuehrerschein_file']['name'])) {
        handleFileUpload($_FILES['fuehrerschein_file'], $upload_dir, $user_id, 'fuehrerschein', $conn);
    }
    if (!empty($_FILES['arbeitsvertrag_file']['name'])) {
        handleFileUpload($_FILES['arbeitsvertrag_file'], $upload_dir, $user_id, 'arbeitsvertrag', $conn);
    }
    if (!empty($_FILES['fuehrungszeugnis_file']['name'])) {
        handleFileUpload($_FILES['fuehrungszeugnis_file'], $upload_dir, $user_id, 'fuehrungszeugnis', $conn);
    }
    if (!empty($_FILES['erstehilfe_file']['name'])) {
        handleFileUpload($_FILES['erstehilfe_file'], $upload_dir, $user_id, 'erstehilfe', $conn);
    }

    // Weiterleitung zurück zum Profil
    header("Location: ../profile.php?id=" . htmlspecialchars($user_id));
    exit;
}
?>
