<?php
include('db.php'); // Verbindung zur Datenbank

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
        $stmt = $conn->query("SELECT * FROM trainings");
        $trainings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($trainings);
    }

    // Aktion: Anmelden
    if ($_POST['action'] == 'anmelden') {
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
        $training_id = $_POST['training_id'];
        $benutzername = $_SESSION['username']; // Benutzername aus der Session

        // Abmeldung des Benutzers vom Training
        $stmt = $conn->prepare("DELETE FROM trainings_anmeldungen WHERE training_id = ? AND benutzername = ?");
        $stmt->execute([$training_id, $benutzername]);

        echo json_encode(['status' => 'abgemeldet']);
    }
}
?>
