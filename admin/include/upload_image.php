<?php
// upload_image.php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    // Überprüfen, ob das CSRF-Token gültig ist
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['error' => 'Ungültiges CSRF-Token']);
        exit;
    }

    $uploadDir = '../uploads/summernote/';
    $file = $_FILES['file'];
    
    // Prüfen, ob das Bild korrekt hochgeladen wurde
    if ($file['error'] === UPLOAD_ERR_OK) {
        $fileName = uniqid() . '-' . basename($file['name']);
        $filePath = $uploadDir . $fileName;
        
        // Bild speichern
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Log-Eintrag für den Upload
            logAction('UPLOAD', 'images', 'Bild hochgeladen: ' . $fileName . ', hochgeladen von: ' . $_SESSION['user_id']);

            // Pfad zurückgeben
            echo json_encode(['url' => 'uploads/summernote/' . $fileName]);
        } else {
            echo json_encode(['error' => 'Fehler beim Speichern des Bildes']);
        }
    } else {
        echo json_encode(['error' => 'Fehler beim Hochladen des Bildes']);
    }
}

// Funktion zum Loggen von Aktionen
function logAction($action, $table, $details) {
    global $conn;

    // SQL-Abfrage zum Einfügen des Log-Eintrags
    $stmt = $conn->prepare("INSERT INTO logs (action, table_name, details, user_id, timestamp) VALUES (:action, :table_name, :details, :user_id, NOW())");
    $stmt->bindParam(':action', $action, PDO::PARAM_STR);
    $stmt->bindParam(':table_name', $table, PDO::PARAM_STR);
    $stmt->bindParam(':details', $details, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
}
?>
