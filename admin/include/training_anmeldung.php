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
        // Logik für Anmeldung
    }

    // Aktion: Abmelden
    if ($_POST['action'] == 'abmelden') {
        // Logik für Abmeldung
    }
}
?>
