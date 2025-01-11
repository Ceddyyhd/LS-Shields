<?php
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

// Überprüfen, ob das CSRF-Token gültig ist
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Ungültiges CSRF-Token']);
    exit;
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

    // Bestandsänderung protokollieren
    if ($stock != $currentStock) {
        $stmt = $conn->prepare("INSERT INTO ausruestung_history (ausruestungstyp_id, old_stock, new_stock, note, changed_by) VALUES (:ausruestungstyp_id, :old_stock, :new_stock, :note, :changed_by)");
        $stmt->execute([
            ':ausruestungstyp_id' => $id,
            ':old_stock' => $currentStock,
            ':new_stock' => $stock,
            ':note' => $note,
            ':changed_by' => $user_id
        ]);
    }

    // Commit der Transaktion
    $conn->commit();

    // Log-Eintrag für die Änderungen
    logAction('UPDATE', 'ausruestungstypen', 'id: ' . $id . ', bearbeitet von: ' . $editor_name);

    echo json_encode(['success' => true, 'message' => 'Ausrüstungstyp erfolgreich bearbeitet']);
} catch (PDOException $e) {
    // Rollback der Transaktion bei Fehler
    $conn->rollBack();
    error_log('Fehler beim Bearbeiten des Ausrüstungstyps: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Fehler beim Bearbeiten des Ausrüstungstyps: ' . $e->getMessage()]);
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
