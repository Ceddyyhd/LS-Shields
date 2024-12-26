<?php
// anmeldung.php

session_start();
include('db.php'); // Verbindung zur DB

// Action: Training erstellen
if ($_POST['action'] == 'training_erstellen') {
    $grund = $_POST['grund'];
    $info = $_POST['info'];
    $leitung = $_POST['leitung'];
    $datum_zeit = $_POST['datum_zeit'];

    // Training in die DB einfügen
    $stmt = $pdo->prepare("INSERT INTO trainings (grund, info, leitung, datum_zeit) VALUES (?, ?, ?, ?)");
    $stmt->execute([$grund, $info, $leitung, $datum_zeit]);

    // Das neue Training zurückgeben
    $training_id = $pdo->lastInsertId();
    echo json_encode(['status' => 'erfolgreich', 'training_id' => $training_id]);
}

// Action: Trainingsdaten abrufen
if ($_POST['action'] == 'get_trainings') {
    $stmt = $pdo->query("SELECT * FROM trainings");
    $trainings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($trainings);
}

// Action: Anmeldung
if ($_POST['action'] == 'anmelden') {
    $training_id = $_POST['training_id'];
    $benutzername = $_SESSION['username'];

    $stmt = $pdo->prepare("INSERT INTO trainings_anmeldungen (training_id, benutzername, status) VALUES (?, ?, ?)");
    $stmt->execute([$training_id, $benutzername, 'angemeldet']);
    echo 'angemeldet';
}

// Action: Abmeldung
if ($_POST['action'] == 'abmelden') {
    $training_id = $_POST['training_id'];
    $benutzername = $_SESSION['username'];

    $stmt = $pdo->prepare("DELETE FROM trainings_anmeldungen WHERE training_id = ? AND benutzername = ?");
    $stmt->execute([$training_id, $benutzername]);
    echo 'abgemeldet';
}
