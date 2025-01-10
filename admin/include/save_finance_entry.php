<?php
include 'db.php';

// Daten aus dem Formular empfangen
$historyData = json_decode($_POST['historyData'], true);
$totalData = json_decode($_POST['totalData'], true);

// Hole den Benutzer, der den Eintrag erstellt hat
$erstellt_von = $_SESSION['username']; // Der Benutzername aus der Session

// 1. Daten in die finanzen_history-Tabelle einfügen
$stmtHistory = $conn->prepare("
    INSERT INTO finanzen_history (user_id, betrag, art, notiz, erstellt_von)
    VALUES (:user_id, :betrag, :art, :notiz, :erstellt_von)
");
$stmtHistory->bindParam(':user_id', $historyData['user_id']);
$stmtHistory->bindParam(':betrag', $historyData['betrag']);
$stmtHistory->bindParam(':art', $historyData['art']);
$stmtHistory->bindParam(':notiz', $historyData['notiz']);
$stmtHistory->bindParam(':erstellt_von', $erstellt_von);
$stmtHistory->execute();

// 2. (Optional) Update für die Gesamtwerte in mitarbeiter_finanzen-Tabelle
$stmtTotal = $conn->prepare("
    UPDATE mitarbeiter_finanzen
    SET gehalt = :gehalt, anteil = :anteil, trinkgeld = :trinkgeld
    WHERE user_id = :user_id
");
$stmtTotal->bindParam(':user_id', $totalData['user_id']);
$stmtTotal->bindParam(':gehalt', $totalData['gehalt']);
$stmtTotal->bindParam(':anteil', $totalData['anteil']);
$stmtTotal->bindParam(':trinkgeld', $totalData['trinkgeld']);
$stmtTotal->execute();

// Erfolgreiche Antwort zurückgeben
echo json_encode(['success' => true, 'message' => 'Eintrag erfolgreich gespeichert!']);
?>
