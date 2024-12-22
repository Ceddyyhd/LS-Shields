<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

// Später Rechteprüfung hier einfügen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Bitte alle Felder ausfüllen.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Ungültige E-Mail-Adresse.']);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (:email, :password)");
    try {
        $stmt->execute([':email' => $email, ':password' => $hashedPassword]);
        echo json_encode(['success' => true, 'message' => 'Benutzer erfolgreich erstellt.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'E-Mail-Adresse bereits registriert.']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage.']);
