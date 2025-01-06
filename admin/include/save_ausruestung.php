<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

include 'db.php';

$user_id = $_POST['user_id'];
$user_name = $_POST['user_name'];

if (isset($_POST['ausruestung']) && is_array($_POST['ausruestung'])) {
    $ausruestung = $_POST['ausruestung'];
} else {
    $ausruestung = [];
    $stmt = $conn->prepare("SELECT key_name FROM ausruestungstypen");
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($items as $item) {
        $ausruestung[$item['key_name']] = 0;
    }
}

session_start();
$canEdit = $_SESSION['permissions']['edit_employee'] ?? false;

if (!$canEdit) {
    echo json_encode(['success' => false, 'message' => 'Keine Berechtigung zum Bearbeiten.']);
    exit;
}

try {
    $conn->beginTransaction();

    foreach ($ausruestung as $key_name => $status) {
        $stmt = $conn->prepare("SELECT stock FROM ausruestungstypen WHERE key_name = :key_name");
        $stmt->execute([':key_name' => $key_name]);
        $stock = $stmt->fetchColumn();

        if ($status == 1 && $stock <= 0) {
            echo json_encode(['success' => false, 'message' => 'Nicht genügend Artikel auf Lager!']);
            exit;
        }

        $stmt = $conn->prepare("SELECT status FROM benutzer_ausruestung WHERE user_id = :user_id AND key_name = :key_name");
        $stmt->execute([':user_id' => $user_id, ':key_name' => $key_name]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($status == 0) {
            if ($existing && $existing['status'] == 1) {
                $stmt = $conn->prepare("UPDATE benutzer_ausruestung SET status = 0 WHERE user_id = :user_id AND key_name = :key_name");
                $stmt->execute([':user_id' => $user_id, ':key_name' => $key_name]);

                $stmt = $conn->prepare("UPDATE ausruestungstypen SET stock = stock + 1 WHERE key_name = :key_name");
                $stmt->execute([':key_name' => $key_name]);

                $stmt = $conn->prepare("INSERT INTO ausruestung_history (user_id, key_name, action, stock_change, editor_name) VALUES (:user_id, :key_name, 'Zurückgabe', 1, :editor_name)");
                $stmt->execute([
                    ':user_id' => $user_id,
                    ':key_name' => $key_name,
                    ':editor_name' => $user_name
                ]);
            }
        } elseif ($status == 1) {
            if ($existing) {
                if ($existing['status'] == 0) {
                    $stmt = $conn->prepare("UPDATE ausruestungstypen SET stock = stock - 1 WHERE key_name = :key_name");
                    $stmt->execute([':key_name' => $key_name]);

                    $stmt = $conn->prepare("UPDATE benutzer_ausruestung SET status = 1 WHERE user_id = :user_id AND key_name = :key_name");
                    $stmt->execute([':user_id' => $user_id, ':key_name' => $key_name]);

                    $stmt = $conn->prepare("INSERT INTO ausruestung_history (user_id, key_name, action, stock_change, editor_name) VALUES (:user_id, :key_name, 'Hinzufügung', -1, :editor_name)");
                    $stmt->execute([
                        ':user_id' => $user_id,
                        ':key_name' => $key_name,
                        ':editor_name' => $user_name
                    ]);
                }
            } else {
                $stmt = $conn->prepare("INSERT INTO benutzer_ausruestung (user_id, key_name, status) VALUES (:user_id, :key_name, 1)");
                $stmt->execute([':user_id' => $user_id, ':key_name' => $key_name]);

                $stmt = $conn->prepare("UPDATE ausruestungstypen SET stock = stock - 1 WHERE key_name = :key_name");
                $stmt->execute([':key_name' => $key_name]);

                $stmt = $conn->prepare("INSERT INTO ausruestung_history (user_id, key_name, action, stock_change, editor_name) VALUES (:user_id, :key_name, 'Hinzufügung', -1, :editor_name)");
                $stmt->execute([
                    ':user_id' => $user_id,
                    ':key_name' => $key_name,
                    ':editor_name' => $user_name
                ]);
            }
        }
    }

    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Änderungen wurden erfolgreich gespeichert!']);
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
}
