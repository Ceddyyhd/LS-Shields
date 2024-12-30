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
        echo "Fehler: Kunden-ID oder Dokumentname fehlt.";
        exit; // Verhindert Weiterleitung bei Fehler
    }

    // Verzeichnis für hochgeladene Dateien
    $upload_dir = '../uploads/';
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            echo "Fehler: Upload-Verzeichnis konnte nicht erstellt werden.";
            exit; // Verhindert Weiterleitung bei Fehler
        }
    }

    // Datei hochladen und speichern
    if (!empty($_FILES['document_file']['name'])) {
        $file = $_FILES['document_file'];
        $file_name = basename($file['name']);
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
        $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];

        if (!in_array(strtolower($file_extension), $allowed_extensions)) {
            echo "Fehler: Ungültiger Dateityp.";
            exit; // Verhindert Weiterleitung bei Fehler
        }

        $unique_name = $custom_name . '_' . uniqid('doc_', true) . '.' . $file_extension;
        $physical_path = $upload_dir . $unique_name;
        $file_path = '/admin/uploads/' . $unique_name;

        if (move_uploaded_file($file['tmp_name'], $physical_path)) {
            echo "Datei erfolgreich hochgeladen.";  // Erfolgsnachricht

            // Dokument in die Datenbank speichern
            $sql = "INSERT INTO documents_customer (user_id, document_name, file_path, uploaded_at, doc_type) 
                    VALUES (:user_id, :file_name, :file_path, NOW(), :doc_type)";
            $stmt = $conn->prepare($sql);
            if ($stmt->execute([
                'user_id' => $customer_id,
                'file_name' => $custom_name,
                'file_path' => $file_path,
                'doc_type' => $doc_type
            ])) {
                echo "Dokument erfolgreich in die Datenbank gespeichert.";  // Erfolgsnachricht
            } else {
                $error = $stmt->errorInfo();
                echo "Fehler bei der SQL-Abfrage: " . print_r($error, true);  // SQL-Fehler ausgeben
                exit; // Verhindert Weiterleitung bei Fehler
            }

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
            if (!$stmt_log->execute([
                'user_id' => $customer_id,
                'document_name' => $custom_name,
                'uploaded_by' => $uploaded_by
            ])) {
                $error_log = $stmt_log->errorInfo();
                echo "Fehler beim Schreiben des Logs: " . print_r($error_log, true);
                exit; // Verhindert Weiterleitung bei Fehler
            }
        } else {
            echo "Fehler beim Verschieben der Datei.";
            exit; // Verhindert Weiterleitung bei Fehler
        }
    } else {
        echo "Fehler: Keine Datei zum Hochladen ausgewählt.";
        exit; // Verhindert Weiterleitung bei Fehler
    }

    // Weiterleitung zur Kunden-Profilseite entfernt, damit der Fehler sichtbar bleibt
    echo "Dokument erfolgreich hochgeladen und gespeichert. Weiterleitung wurde entfernt.";
}
?>
