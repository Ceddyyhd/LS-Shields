<?php
include 'security_check.php'; // Sicherheitsprüfung für diese Datei

// Fehlerprotokollierung aktivieren (für Debugging)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Datenbankverbindung einbinden
include 'db.php';

// Überprüfen, ob der Benutzer eingeloggt ist
session_start();

// Wenn der Benutzer nicht eingeloggt ist, dann nichts tun
if (!isset($_SESSION['user_id'])) {
    die("Kein Benutzer eingeloggt.");
}

// Empfangen der Formulardaten
$id = $_POST['id']; // ID des Ausrüstungstyps
$key_name = $_POST['key_name'];
$display_name = $_POST['display_name'];
$category = $_POST['category'];
$description = $_POST['description'];
$stock = $_POST['stock'];
$note = $_POST['note']; // Notiz zur Bestandsänderung
$user_name = $_POST['user_name']; // Benutzernamen vom versteckten Input
$user_id = $_SESSION['user_id']; // Benutzer-ID aus der Session
$editor_name = $_POST['editor_name']; // Editor-Name aus dem versteckten Input

try {
    // Beginne die Transaktion
    $conn->beginTransaction();

    // Holen des aktuellen Bestands
    $stmt = $conn->prepare("SELECT stock FROM ausruestungstypen WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $currentStock = $stmt->fetchColumn();

    // Update des Ausrüstungstyps in der Tabelle
    $stmt = $conn->prepare("UPDATE ausruestungstypen SET key_name = :key_name, display_name = :display_name, category = :category, description = :description WHERE id = :id");
    $stmt->execute([
        ':id' => $id,
        ':key_name' => $key_name,
        ':display_name' => $display_name,
        ':category' => $category,
        ':description' => $description
    ]);

    // Bestandsänderung aktualisieren
    $stmt = $conn->prepare("UPDATE ausruestungstypen SET stock = :stock WHERE id = :id");
    $stmt->execute([
        ':id' => $id,
        ':stock' => (int)$stock // Stellen Sie sicher, dass stock als Zahl behandelt wird
    ]);

    // History-Eintrag erstellen (Bestand geändert + Notiz zusammen)
    $action = "Bestand geändert";
    if (!empty($note)) {
        $action .= " ($note)"; // Füge die Notiz zur Aktion hinzu
    }

    // Füge die Bestandsänderung in der History hinzu
    $action .= " (Alter Bestand: $currentStock -> Neuer Bestand: $stock)"; // Zeige alte und neue Bestandszahlen an

    // History-Eintrag für Bestandsänderung
    $stmt = $conn->prepare("INSERT INTO ausruestung_history (user_id, key_name, action, stock_change, editor_name) VALUES (:user_id, :key_name, :action, :stock_change, :editor_name)");
    $stmt->execute([
        ':user_id' => $user_id,
        ':key_name' => $key_name,
        ':action' => $action, // Aktion enthält jetzt auch die Notiz und Bestandsänderung
        ':stock_change' => (int)$stock, // Bestandsänderung
        ':editor_name' => $editor_name
    ]);

    // Alle Änderungen abschließen
    $conn->commit();

    // Erfolgreiche Antwort zurückgeben
    echo json_encode(['success' => true, 'message' => 'Änderungen wurden erfolgreich gespeichert!']);
} catch (Exception $e) {
    // Im Fehlerfall die Transaktion zurückrollen
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
}
?>
