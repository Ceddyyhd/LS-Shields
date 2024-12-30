<?php
// Datenbankverbindung einbinden
include 'db.php';
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Überprüfen, ob das Formular abgeschickt wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kunden-ID und benutzerdefinierter Name überprüfen
    $customer_id = $_POST['user_id'] ?? null;
    $custom_name = $_POST['document_name'] ?? null;
    $doc_type = $_POST['doc_type'] ?? 'unbekannt';

    if (!$customer_id || !$custom_name) {
        die("Kunden-ID oder Dokumentname fehlt.");
    }

    // Verzeichnis für hochgeladene Dateien
    $upload_dir = '../uploads/customer';
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
        $file_path = '/admin/uploads/customer' . $unique_name;

        if (move_uploaded_file($file['tmp_name'], $physical_path)) {
            // Dokument in die Datenbank speichern
            $sql = "INSERT INTO documents_customer (user_id, document_name, file_path, uploaded_at, doc_type) 
                    VALUES (:user_id, :file_name, :file_path, NOW(), :doc_type)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'user_id' => $customer_id,
                'file_name' => $custom_name,
                'file_path' => $file_path,
                'doc_type' => $doc_type
            ]);

            // Benutzernamen ermitteln
            $uploaded_by = $_SESSION['username'] ?? null;

            if (!$uploaded_by) {
                // Benutzername aus der Datenbank abrufen, falls nicht in der Session gespeichert
                $stmt_user = $conn->prepare("SELECT name FROM kunden WHERE id = :id");
                $stmt_user->execute([':id' => $customer_id]);
                $user = $stmt_user->fetch(PDO::FETCH_ASSOC);
                $uploaded_by = $user['name'] ?? 'Unbekannt';
            }

            // Log in die Datenbank schreiben
            $sql_log = "INSERT INTO upload_customer_logs (user_id, document_name, uploaded_by, created_at) 
                        VALUES (:user_id, :document_name, :uploaded_by, NOW())";
            $stmt_log = $conn->prepare($sql_log);
            $stmt_log->execute([
                'user_id' => $customer_id,
                'document_name' => $custom_name,
                'uploaded_by' => $uploaded_by
            ]);
        } else {
            echo "Fehler beim Hochladen der Datei.";  // Fehlernachricht
        }
    }

    // Weiterleitung zur Kunden-Profilseite
    exit;
}
?>
