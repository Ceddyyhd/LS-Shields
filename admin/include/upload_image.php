<?php
// upload_image.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $uploadDir = '../uploads/summernote/';
    $file = $_FILES['file'];
    
    // Prüfen, ob das Bild korrekt hochgeladen wurde
    if ($file['error'] === UPLOAD_ERR_OK) {
        $fileName = uniqid() . '-' . basename($file['name']);
        $filePath = $uploadDir . $fileName;
        
        // Bild speichern
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Pfad zurückgeben
            echo json_encode(['url' => 'uploads/summernote/' . $fileName]);
        } else {
            echo json_encode(['error' => 'Fehler beim Speichern des Bildes']);
        }
    } else {
        echo json_encode(['error' => 'Fehler beim Hochladen des Bildes']);
    }
}


?>
