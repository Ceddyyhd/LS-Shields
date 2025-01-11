<?php
include('db.php'); // Verbindung zur Datenbank

// Session starten, falls noch nicht gestartet
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Überprüfen, ob das CSRF-Token gültig ist
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['status' => 'fehlgeschlagen', 'message' => 'Ungültiges CSRF-Token']);
        exit;
    }

    // Aktion: Training erstellen
    if ($_POST['action'] == 'training_erstellen') {
        $grund = $_POST['grund'];
        $info = $_POST['info'];
        $leitung = $_POST['leitung'];
        $datum_zeit = $_POST['datum_zeit'];

        try {
            $stmt = $conn->prepare("INSERT INTO trainings (grund, info, leitung, datum_zeit) VALUES (?, ?, ?, ?)");
            $stmt->execute([$grund, $info, $leitung, $datum_zeit]);

            // Log-Eintrag für das Erstellen des Trainings
            logAction('INSERT', 'trainings', 'Training erstellt: ' . $grund . ', erstellt von: ' . $_SESSION['user_id']);

            echo json_encode(['status' => 'erfolgreich']);
        } catch (PDOException $e) {
            error_log('Fehler beim Erstellen des Trainings: ' . $e->getMessage());
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
                // Abfrage für den Anmeldestatus
                $stmt = $conn->prepare("SELECT COUNT(*) FROM trainings_anmeldungen WHERE training_id = ? AND benutzername = ?");
                $stmt->execute([$training['id'], $_SESSION['username']]);
                $isEnrolled = $stmt->fetchColumn();

                // Anmeldestatus hinzufügen
                $training['is_enrolled'] = ($isEnrolled > 0);

                // Mitarbeiter für dieses Training abrufen (Benutzernamen aus der `trainings_anmeldungen`-Tabelle)
                $stmt = $conn->prepare("SELECT benutzername FROM trainings_anmeldungen WHERE training_id = ?");
                $stmt->execute([$training['id']]);
                $mitarbeiter = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Mitarbeiter hinzufügen
                $training['mitarbeiter'] = $mitarbeiter;
            }

            echo json_encode(['status' => 'erfolgreich', 'trainings' => $trainings]);
        } catch (PDOException $e) {
            error_log('Fehler beim Abrufen der Trainings: ' . $e->getMessage());
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
    // Aktion: Training löschen
    if ($_POST['action'] == 'delete_training') {
        $training_id = $_POST['training_id'];
    
        try {
            $stmt = $conn->prepare("DELETE FROM trainings WHERE id = ?");
            $stmt->execute([$training_id]);
            echo json_encode(['status' => 'erfolgreich']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'fehlgeschlagen', 'error' => $e->getMessage()]);
        }
    }
}

// Funktion zum Loggen von Aktionen
function logAction($action, $table, $details) {
    global $conn;

    // SQL-Abfrage zum Einfügen des Log-Eintrags
    $stmt = $conn->prepare("INSERT INTO logs (action, table_name, details, user_id, timestamp) VALUES (:action, :table_name, :details, :user_id, NOW())");
    $stmt->bindParam(':action', $action, PDO::PARAM_STR);
    $stmt->bindParam(':table_name', $table, PDO::PARAM_STR);
    $stmt->bindParam(':details', $details, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
}
?>
