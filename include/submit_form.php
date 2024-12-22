<?php
// Verbindung zur Datenbank herstellen
$servername = "localhost";
$username = "LS-Shields";
$password = "g%g95i52A";
$dbname = "LS-Shields";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $vornameNachname = htmlspecialchars($_POST['vorname_nachname']);
        $telefonnummer = htmlspecialchars($_POST['telefonnummer']);
        $anfrage = htmlspecialchars($_POST['anfrage']);

        // Daten in die Datenbank einfügen
        $sql = "INSERT INTO anfragen (vorname_nachname, telefonnummer, anfrage) 
                VALUES (:vorname_nachname, :telefonnummer, :anfrage)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':vorname_nachname' => $vornameNachname,
            ':telefonnummer' => $telefonnummer,
            ':anfrage' => $anfrage,
        ]);

        // Erfolgsnachricht senden
        echo 'Ihre Anfrage wurde erfolgreich gesendet!';
    }
} catch (PDOException $e) {
    // Fehlermeldung senden
    echo 'Fehler: ' . $e->getMessage();
}
?>