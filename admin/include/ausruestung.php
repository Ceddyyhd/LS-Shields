<?php
// Berechtigungsprüfung
$canEdit = $_SESSION['permissions']['edit_employee'] ?? false;
$editor_name = $_SESSION['user_name'] ?? 'Unbekannt'; // Fallback-Wert, falls 'user_name' nicht existiert

// Ausrüstungstypen und Benutzer-Ausrüstung abrufen
$stmt = $conn->prepare("SELECT key_name, display_name, category FROM ausruestungstypen ORDER BY category");
$stmt->execute();
$ausruestungstypen = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT key_name, status FROM benutzer_ausruestung WHERE user_id = :user_id");
$stmt->execute([':user_id' => $user_id]);
$benutzerAusrüstung = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Abrufen der letzten Spind Kontrolle und Notiz für den Benutzer
$stmt = $conn->prepare("SELECT letzte_spind_kontrolle, notizen FROM spind_kontrolle_notizen WHERE user_id = :user_id");
$stmt->execute([':user_id' => $user_id]);
$benutzerSpind_Kontrolle = $stmt->fetch(PDO::FETCH_ASSOC);

// Falls keine Daten vorhanden sind, Standardwerte setzen
$letzte_spind_kontrolle = $benutzerSpind_Kontrolle['letzte_spind_kontrolle'] ?? '';
$notizen = $benutzerSpind_Kontrolle['notizen'] ?? '';

// Benutzer-Ausrüstung in ein Array umwandeln
$userAusrüstung = [];
foreach ($benutzerAusrüstung as $item) {
    $userAusrüstung[$item['key_name']] = (int)$item['status'];
}

// Nach Kategorien gruppieren
$categories = [];
foreach ($ausruestungstypen as $item) {
    $categories[$item['category']][] = $item;
}

// Rückgabe der abgerufenen Daten als Array
return [
    'canEdit' => $canEdit,
    'categories' => $categories,
    'userAusrüstung' => $userAusrüstung,
    'letzte_spind_kontrolle' => $letzte_spind_kontrolle,
    'notizen' => $notizen
];
