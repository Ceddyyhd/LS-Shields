<?php
session_start();
include 'db.php'; // Deine Datenbankverbindung

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Überprüfe, ob der Benutzername in der Session vorhanden ist
    if (isset($_SESSION['user_name'])) {
        $erstellt_von = $_SESSION['user_name'];
    } else {
        echo json_encode(["status" => "error", "message" => "Benutzer nicht angemeldet."]);
        exit();
    }

    // Eingabewerte aus dem Formular holen
    $typ = $_POST['typ'];
    $kategorie = $_POST['kategorie'];
    $notiz = $_POST['notiz'];
    $betrag = $_POST['betrag'];

    // SQL-Query, um den neuen Eintrag hinzuzufügen
    $sql = "INSERT INTO finanzen (typ, kategorie, notiz, betrag, erstellt_von) 
            VALUES ('$typ', '$kategorie', '$notiz', '$betrag', '$erstellt_von')";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["status" => "success", "message" => "Eintrag erfolgreich hinzugefügt!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Fehler: " . mysqli_error($conn)]);
    }
}
?>
