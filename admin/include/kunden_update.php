<?php
include 'security_check.php'; // Sicherheitsprüfung für diese Datei

include('db.php');

// Kunden-ID und neuer Status (geloescht) aus dem Formular erhalten
$kundenId = $_POST['kunden_id'];  // Kunden-ID
$status = 'geloescht';  // Markiere den Kunden als gelöscht

// SQL-Befehl zum Aktualisieren des Kundenstatus
$stmt = $conn->prepare("UPDATE Kunden SET geloescht = ? WHERE id = ?");
$stmt->execute([$status, $kundenId]);

echo json_encode(['status' => 'success', 'message' => 'Kunde wurde als gelöscht markiert.']);
?>
