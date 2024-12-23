<?php
// Datenbankverbindung einbinden
include 'db.php';
session_start();
echo '<pre>';
print_r($_SESSION);
echo '</pre>';
die();

// Überprüfen, ob das Formular abgeschickt wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Benutzer-ID und benutzerdefinierter Name überprüfen
    $user_id = $_POST['user_id'] ?? null;
    $custom_name = $_POST['document_name'] ?? null;
    $doc_type = $_POST['doc_type'] ?? 'unbekannt';

    if (!$user_id || !$custom_name) {
        die("Benutzer-ID oder Dokumentname fehlt.");
    }

    // Verzeichnis für hochgeladene Dateien
    $upload_dir = '../uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Datei hochladen und speichern
    if (!empty($_FILES['document_file']['name'])) {
        $file = $_FILES['document_file'];
        $file_name = basename($file['name']);
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
        $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];

        if (!in_array(strtolower($file_extension), $allowed_extensions)) {
            die("Ungültiger Dateityp.");
        }

        $unique_name = $custom_name . '_' . uniqid('doc_', true) . '.' . $file_extension;
        $physical_path = $upload_dir . $unique_name;
        $file_path = '/admin/uploads/' . $unique_name;

        if (move_uploaded_file($file['tmp_name'], $physical_path)) {
            // Dokument in die Datenbank speichern
            $sql = "INSERT INTO documents (user_id, file_name, file_path, uploaded_at, doc_type) 
                    VALUES (:user_id, :file_name, :file_path, NOW(), :doc_type)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'user_id' => $user_id,
                'file_name' => $custom_name,
                'file_path' => $file_path,
                'doc_type' => $doc_type
            ]);

            // Log in die Datenbank schreiben
            $uploaded_by = $_SESSION['username'] ?? 'Unbekannt'; // Angemeldeter Benutzername aus der Session
            $sql_log = "INSERT INTO upload_logs (user_id, uploaded_by, document_name, upload_time) 
                        VALUES (:user_id, :uploaded_by, :document_name, NOW())";
            $stmt_log = $conn->prepare($sql_log);
            $stmt_log->execute([
                'user_id' => $user_id,
                'uploaded_by' => $uploaded_by,
                'document_name' => $custom_name
            ]);
        }
    }

    // Weiterleitung zur Profilseite
    header("Location: ../profile.php?id=" . htmlspecialchars($user_id));
    exit;
}
?>
