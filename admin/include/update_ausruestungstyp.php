<?php
// Fehlerprotokollierung aktivieren (für Debugging)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Datenbankverbindung einbinden
include 'db.php';

// Empfangen der Formulardaten
$id = $_POST['id']; // ID des Ausrüstungstyps
$key_name = $_POST['key_name'];
$display_name = $_POST['display_name'];
$category = $_POST['category'];
$description = $_POST['description'];
$stock = $_POST['stock'];
$note = $_POST['note']; // Notiz für die History
$user_id = $_SESSION['user_id']; // Benutzer-ID
$editor_name = $_SESSION['user_name']; // Benutzername (Editor)

try {
    // Beginne die Transaktion
    $conn->beginTransaction();

    // Hole den aktuellen Bestand aus der Datenbank, um die Bestandsänderung zu berechnen
    $stmt = $conn->prepare("SELECT stock FROM ausruestungstypen WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $existingStock = $stmt->fetchColumn();

    // Berechne die Bestandsänderung
    $stockChange = $stock - $existingStock;

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
        ':stock' => $stock
    ]);

    // History-Eintrag für die Bestandsänderung erstellen
    $stmt = $conn->prepare("INSERT INTO ausruestung_history (user_id, key_name, action, stock_change, editor_name) VALUES (:user_id, :key_name, 'Bestand geändert', :stock_change, :editor_name)");
    $stmt->execute([
        ':user_id' => $user_id,
        ':key_name' => $key_name,
        ':stock_change' => $stockChange, // Bestandsänderung
        ':editor_name' => $editor_name
    ]);

    // Wenn eine Notiz hinzugefügt wurde, speichern wir sie auch in der History
    if (!empty($note)) {
        $stmt = $conn->prepare("INSERT INTO ausruestung_history (user_id, key_name, action, stock_change, editor_name) VALUES (:user_id, :key_name, :note, 0, :editor_name)");
        $stmt->execute([
            ':user_id' => $user_id,
            ':key_name' => $key_name,
            ':note' => $note, // Notiz
            ':editor_name' => $editor_name
        ]);
    }

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
