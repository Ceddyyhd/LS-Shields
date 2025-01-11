<?php
session_start(); // Starten der Session, um Cooldown-Daten zu speichern

// Verbindung zur Datenbank herstellen
include 'db.php';

$cooldownTime = 60; // Cooldown in Sekunden
$currentTime = time();

// Überprüfen, ob der Benutzer einen Cooldown hat
if (isset($_SESSION['last_submit_time'])) {
    $timeSinceLastSubmit = $currentTime - $_SESSION['last_submit_time'];

    if ($timeSinceLastSubmit < $cooldownTime) {
        $remainingTime = $cooldownTime - $timeSinceLastSubmit;
        echo json_encode(['success' => false, 'message' => "Bitte warte noch $remainingTime Sekunden, bevor du erneut eine Anfrage sendest."]);
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $vornameNachname = filter_input(INPUT_POST, 'vorname_nachname', FILTER_SANITIZE_STRING);
    $telefonnummer = filter_input(INPUT_POST, 'telefonnummer', FILTER_SANITIZE_STRING);
    $anfrage = filter_input(INPUT_POST, 'anfrage', FILTER_SANITIZE_STRING);

    if (!$vornameNachname || !$telefonnummer || !$anfrage) {
        echo json_encode(['success' => false, 'message' => 'Bitte füllen Sie alle Felder korrekt aus.']);
        exit;
    }

    // Daten in die Datenbank einfügen
    $sql = "INSERT INTO anfragen (vorname_nachname, telefonnummer, anfrage) 
            VALUES (:vorname_nachname, :telefonnummer, :anfrage)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':vorname_nachname' => $vornameNachname,
        ':telefonnummer' => $telefonnummer,
        ':anfrage' => $anfrage,
    ]);

    // Cooldown setzen
    $_SESSION['last_submit_time'] = $currentTime;

    // Erfolgsnachricht senden
    echo json_encode(['success' => true, 'message' => 'Ihre Anfrage wurde erfolgreich gesendet!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage.']);
}
?>