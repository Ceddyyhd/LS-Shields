<?php
// Datenbankverbindung einbinden
include 'db.php';

// Überprüfen, ob alle benötigten Felder gesendet wurden
if (isset($_POST['unternehmen_name'], $_POST['ansprechperson_name'], $_POST['ansprechperson_nummer'], $_POST['adresse'], $_POST['unternehmen_art'])) {

    // Werte aus dem Formular holen
    $unternehmen_name = $_POST['unternehmen_name'];
    $ansprechperson_name = $_POST['ansprechperson_name'];
    $ansprechperson_nummer = $_POST['ansprechperson_nummer'];
    $adresse = $_POST['adresse'];
    $unternehmen_art = $_POST['unternehmen_art'];

    // SQL-Query zum Einfügen der Daten
    $query = "INSERT INTO kunden (unternehmen_name, ansprechperson_name, ansprechperson_nummer, adresse, unternehmen_art) 
              VALUES (:unternehmen_name, :ansprechperson_name, :ansprechperson_nummer, :adresse, :unternehmen_art)";
    $stmt = $conn->prepare($query);

    // Parameter binden
    $stmt->bindParam(':unternehmen_name', $unternehmen_name);
    $stmt->bindParam(':ansprechperson_name', $ansprechperson_name);
    $stmt->bindParam(':ansprechperson_nummer', $ansprechperson_nummer);
    $stmt->bindParam(':adresse', $adresse);
    $stmt->bindParam(':unternehmen_art', $unternehmen_art);

    // Ausführen und überprüfen, ob die Anfrage erfolgreich war
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Erstellen des Kunden']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Alle Felder sind erforderlich']);
}
?>
