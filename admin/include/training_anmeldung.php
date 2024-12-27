<?php
include('db.php'); // Verbindung zur Datenbank

// Session starten, falls noch nicht gestartet
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Aktion: Training erstellen
    if ($_POST['action'] == 'training_erstellen') {
        $grund = $_POST['grund'];
        $info = $_POST['info'];
        $leitung = $_POST['leitung'];
        $datum_zeit = $_POST['datum_zeit'];

        try {
            $stmt = $conn->prepare("INSERT INTO trainings (grund, info, leitung, datum_zeit) VALUES (?, ?, ?, ?)");
            $stmt->execute([$grund, $info, $leitung, $datum_zeit]);
            echo json_encode(['status' => 'erfolgreich']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'fehlgeschlagen', 'error' => $e->getMessage()]);
        }
    }

    // Aktion: Trainings abrufen
if ($_POST['action'] == 'get_trainings') {
    try {
        // Alle Trainings abrufen
        $stmt = $conn->query("SELECT * FROM trainings");
        $trainings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Den Anmeldestatus und die Mitarbeiter für jedes Training hinzufügen
        foreach ($trainings as &$training) {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM trainings_anmeldungen WHERE training_id = ? AND benutzername = ?");
            $stmt->execute([$training['id'], $_SESSION['username']]);
            $isEnrolled = $stmt->fetchColumn();

            // Füge das Anmeldestatus-Feld hinzu
            $training['is_enrolled'] = ($isEnrolled > 0);

            // Mitarbeiter für dieses Training abrufen
            $stmt = $conn->prepare("SELECT name FROM mitarbeiter WHERE training_id = ?");
            $stmt->execute([$training['id']]);
            $training['mitarbeiter'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Gebe die Trainings als JSON zurück
        echo json_encode($trainings);
    } catch (PDOException $e) {
        // Fehlerbehandlung
        echo json_encode(['status' => 'fehlgeschlagen', 'error' => $e->getMessage()]);
    }
}

    // Aktion: Anmelden
    if ($_POST['action'] == 'anmelden') {
        // Überprüfen, ob der Benutzer eingeloggt ist
        if (!isset($_SESSION['username'])) {
            echo json_encode(['status' => 'fehler', 'error' => 'Benutzer nicht eingeloggt']);
            exit; // Stoppe das Skript, falls der Benutzer nicht eingeloggt ist
        }

        $training_id = $_POST['training_id'];
        $benutzername = $_SESSION['username']; // Benutzername aus der Session

        // Überprüfen, ob der Benutzer bereits für das Training angemeldet ist
        $stmt = $conn->prepare("SELECT COUNT(*) FROM trainings_anmeldungen WHERE training_id = ? AND benutzername = ?");
        $stmt->execute([$training_id, $benutzername]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            // Benutzer ist bereits angemeldet
            echo json_encode(['status' => 'already_enrolled']);
        } else {
            // Benutzer für das Training anmelden
            $stmt = $conn->prepare("INSERT INTO trainings_anmeldungen (training_id, benutzername, status) VALUES (?, ?, ?)");
            $stmt->execute([$training_id, $benutzername, 'angemeldet']);
            echo json_encode(['status' => 'angemeldet']);
        }
    }

    // Aktion: Abmelden
    if ($_POST['action'] == 'abmelden') {
        // Überprüfen, ob der Benutzer eingeloggt ist
        if (!isset($_SESSION['username'])) {
            echo json_encode(['status' => 'fehler', 'error' => 'Benutzer nicht eingeloggt']);
            exit; // Stoppe das Skript, falls der Benutzer nicht eingeloggt ist
        }

        $training_id = $_POST['training_id'];
        $benutzername = $_SESSION['username']; // Benutzername aus der Session

        // Abmeldung des Benutzers vom Training
        $stmt = $conn->prepare("DELETE FROM trainings_anmeldungen WHERE training_id = ? AND benutzername = ?");
        $stmt->execute([$training_id, $benutzername]);

        echo json_encode(['status' => 'abgemeldet']);
    }
}
?>
