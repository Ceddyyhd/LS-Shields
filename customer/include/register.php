<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require 'db.php';

    $umail = $_POST['umail'];
    $name = $_POST['name'];
    $nummer = $_POST['nummer'];
    $kontonummer = $_POST['kontonummer'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $remember_token = bin2hex(random_bytes(32)); // Für das Remember Me Token
    $profile_image = 'uploads/profile_images/standard.png';

    // SQL-Statement für die Registrierung
    $stmt = $conn->prepare("INSERT INTO kunden (umail, name, nummer, kontonummer, password, remember_token, profile_image) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('sssssss', $umail, $name, $nummer, $kontonummer, $password, $remember_token, $profile_image);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Registrierung erfolgreich!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Fehler bei der Registrierung!"]);
    }

    $stmt->close();
    $conn->close();
}
?>
