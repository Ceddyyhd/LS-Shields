<?php
// Überprüfen, ob die Anfrage von der richtigen Quelle kommt (ls-shields.ceddyyhd2.eu)
$allowed_domain = 'ls-shields.ceddyyhd2.eu';
$host = $_SERVER['HTTP_HOST']; // Holt den Host der aktuellen Anfrage

// Sicherstellen, dass die Anfrage von der erlaubten Domain kommt
if (strpos($host, $allowed_domain) === false) {
    // Falls die Anfrage nicht von der erlaubten Domain kommt, Weiterleitung zur Fehlerseite
    header('Location: ../error.php');
    exit;
}

include 'db.php';
session_start();

header('Content-Type: application/json');

// Eingabedaten abrufen
$name = $_POST['name'] ?? null;
$level = $_POST['level'] ?? null;
$value = $_POST['value'] ?? null;
$permissions = json_decode($_POST['permissions'], true); // JSON-Daten dekodieren

if (!$name || !$level || !$value || !is_array($permissions)) {
    echo json_encode(['success' => false, 'message' => 'Ungültige Eingaben.']);
    exit;
}

try {
    // Hole den Wert des eingeloggten Benutzers
    $stmt = $conn->prepare("SELECT roles.value FROM roles 
                            JOIN users ON users.role_id = roles.id 
                            WHERE users.id = :user_id");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $currentUserRole = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$currentUserRole) {
        echo json_encode(['success' => false, 'message' => 'Benutzerrolle nicht gefunden.']);
        exit;
    }

    $currentUserValue = (int) $currentUserRole['value'];

    // Überprüfen, ob der neue Wert den Wert des aktuellen Benutzers überschreitet
    if ($value > $currentUserValue) {
        echo json_encode(['success' => false, 'message' => 'Sie können keine Rolle mit einem höheren Wert erstellen als Ihre eigene Rolle.']);
        exit;
    }

    // Rolle in die Datenbank einfügen
    $stmt = $conn->prepare("INSERT INTO roles (name, level, value, permissions) VALUES (:name, :level, :value, :permissions)");
    $stmt->execute([
        ':name' => $name,
        ':level' => $level,
        ':value' => $value,
        ':permissions' => json_encode($permissions)
    ]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
}

?>
