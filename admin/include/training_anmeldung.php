<?php
// db.php einbinden, um die Verbindung zur Datenbank zu nutzen
include('db.php'); // Hier wird die Verbindung über $conn geladen

session_start();

// Die Aktion, die ausgeführt werden soll (z. B. Training erstellen, anmelden, abmelden)
$action = $_POST['action'] ?? '';

if ($action == 'training_erstellen') {
    // Die vom Frontend übermittelten Daten (Grund, Info, Leitung, Datum/Uhrzeit)
    $grund = $_POST['grund'];
    $info = $_POST['info'];
    $leitung = $_POST['leitung'];
    $datum_zeit = $_POST['datum_zeit'];

    // Versuche, das Training in die Datenbank einzufügen
    try {
        $stmt = $conn->prepare("INSERT INTO trainings (grund, info, leitung, datum_zeit) VALUES (?, ?, ?, ?)");
        $stmt->execute([$grund, $info, $leitung, $datum_zeit]);

        // Rückgabe der Trainings-ID für spätere Verwendung (z. B. bei der Anmeldung)
        $training_id = $conn->lastInsertId();
        echo json_encode(['status' => 'erfolgreich', 'training_id' => $training_id]);
    } catch (PDOException $e) {
        // Fehlerbehandlung, falls die Datenbankoperation fehlschlägt
        echo json_encode(['status' => 'fehlgeschlagen', 'error' => $e->getMessage()]);
    }
}

// Wenn die Aktion "get_trainings" ist, werden alle Trainings abgerufen
if ($action == 'get_trainings') {
    try {
        $stmt = $conn->query("SELECT * FROM trainings");
        $trainings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($trainings);
    } catch (PDOException $e) {
        // Fehlerbehandlung, falls etwas schiefgeht
        echo json_encode(['status' => 'fehlgeschlagen', 'error' => $e->getMessage()]);
    }
}

// Anmeldung zu einem Training
if ($action == 'anmelden') {
    $training_id = $_POST['training_id'];
    $benutzername = $_SESSION['username'];

    try {
        $stmt = $conn->prepare("INSERT INTO trainings_anmeldungen (training_id, benutzername, status) VALUES (?, ?, ?)");
        $stmt->execute([$training_id, $benutzername, 'angemeldet']);
        echo 'angemeldet';
    } catch (PDOException $e) {
        echo json_encode(['status' => 'fehlgeschlagen', 'error' => $e->getMessage()]);
    }
}

// Abmeldung von einem Training
if ($action == 'abmelden') {
    $training_id = $_POST['training_id'];
    $benutzername = $_SESSION['username'];

    try {
        $stmt = $conn->prepare("DELETE FROM trainings_anmeldungen WHERE training_id = ? AND benutzername = ?");
        $stmt->execute([$training_id, $benutzername]);
        echo 'abgemeldet';
    } catch (PDOException $e) {
        echo json_encode(['status' => 'fehlgeschlagen', 'error' => $e->getMessage()]);
    }
}
?>
