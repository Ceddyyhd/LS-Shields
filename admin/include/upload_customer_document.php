<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Überprüfen, ob die Datei hochgeladen wurde
    if (isset($_FILES['document_file']) && $_FILES['document_file']['error'] == 0) {
        // Daten aus dem Formular
        $customer_id = $_POST['customer_id'];
        $document_name = $_POST['document_name'];
        $file_tmp = $_FILES['document_file']['tmp_name'];
        $file_name = $_FILES['document_file']['name'];
        $file_path = 'uploads/' . basename($file_name);

        // Verschiebe die Datei ins Zielverzeichnis
        if (move_uploaded_file($file_tmp, $file_path)) {
            // Datei-Informationen in die Datenbank einfügen
            $stmt = $conn->prepare("INSERT INTO customer_documents (customer_id, document_name, file_path) VALUES (?, ?, ?)");
            $stmt->execute([$customer_id, $document_name, $file_path]);

            echo json_encode(['success' => true, 'message' => 'Dokument erfolgreich hochgeladen.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Fehler beim Hochladen der Datei.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Keine Datei ausgewählt oder Fehler beim Hochladen.']);
    }
}
?>
