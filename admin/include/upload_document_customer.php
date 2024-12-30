<?php
// Fehlerprotokollierung aktivieren
ini_set('display_errors', 1);  // Fehlerprotokollierung aktivieren
error_reporting(E_ALL);  // Alle Fehler anzeigen

// Datenbankverbindung einbinden
include 'db.php';
session_start();

// Überprüfen, ob das Formular abgeschickt wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kunden-ID und benutzerdefinierter Name überprüfen
    $customer_id = $_POST['user_id'] ?? null;
    $custom_name = $_POST['document_name'] ?? null;
    $doc_type = $_POST['doc_type'] ?? 'unbekannt';

    if (!$customer_id || !$custom_name) {
        die("Fehler: Kunden-ID oder Dokumentname fehlt.");
    }

    // Verzeichnis für hochgeladene Dateien
    $upload_dir = '../uploads/customer/';
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
            die("Fehler: Ungültiger Dateityp.");
        }

        $unique_name = $custom_name . '_' . uniqid('doc_', true) . '.' . $file_extension;
        $physical_path = $upload_dir . $unique_name;
        $file_path = '/admin/uploads/customer/' . $unique_name;

        if (move_uploaded_file($file['tmp_name'], $physical_path)) {
            // Dokument in die Datenbank speichern
            $sql = "INSERT INTO documents_customer (user_id, file_name, file_path, uploaded_at, doc_type) 
                    VALUES (:user_id, :file_name, :file_path, NOW(), :doc_type)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'user_id' => $customer_id,
                'file_name' => $file_name,  // Hier wird file_name verwendet, da document_name in der DB nicht existiert
                'file_path' => $file_path,
                'doc_type' => $doc_type
            ]);
        }
    }

    // Weiterleitung zur Kunden-Profilseite entfernt, damit der Fehler sichtbar bleibt
    echo "Dokument erfolgreich hochgeladen und gespeichert.";
    exit;
}
?>
