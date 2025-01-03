<?php
// upload_image.php

// Überprüfen, ob die Datei hochgeladen wurde
if ($_FILES['file']['error'] == UPLOAD_ERR_OK) {
    // Zielverzeichnis zum Speichern des Bildes
    $uploadDir = '../uploads/summernote/';
    // Ziel-Dateiname
    $fileName = basename($_FILES['file']['name']);
    $targetFile = $uploadDir . $fileName;

    // Datei hochladen
    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
        // Erfolgreich hochgeladen, gebe den relativen Dateipfad zurück
        echo json_encode(['filePath' => $targetFile]);  // Kein "file://" Präfix
    } else {
        echo json_encode(['error' => 'Fehler beim Hochladen des Bildes.']);
    }
} else {
    echo json_encode(['error' => 'Kein Bild hochgeladen.']);
}

?>
