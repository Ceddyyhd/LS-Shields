<?php
session_start();

// Erneute Session-ID generieren, um Session-Fixation zu vermeiden
session_regenerate_id(true);

// HTTP-Header, um Caching und das Speichern von Seiten zu verhindern
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include 'include/db.php';
include 'auth.php'; // Authentifizierungslogik einbinden

// Session-Wiederherstellung prüfen (wenn "Remember Me" verwendet wird)
restoreSessionIfRememberMe($conn);

// Überprüfen, ob der Benutzer eingeloggt ist
if (!isset($_SESSION['user_id'])) {
    // Prüfen, ob ein "Remember Me"-Cookie existiert
    if (isset($_COOKIE['remember_me'])) {
        $token = $_COOKIE['remember_me'];

        // Token in der Datenbank prüfen
        $stmt = $conn->prepare("SELECT id FROM users WHERE remember_token = :token");
        $stmt->execute([':token' => $token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Benutzer automatisch einloggen
            $_SESSION['user_id'] = $user['id'];
        } else {
            // Ungültiges Token -> Cookie löschen
            setcookie('remember_me', '', time() - 3600, '/');
        }
    }

    // Wenn keine Anmeldung vorhanden ist, zur Login-Seite umleiten
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.html');
        exit;
    }
}

// Überprüfen, ob der Benutzer in der `user_sessions`-Tabelle eingeloggt ist
$query = "SELECT * FROM user_sessions WHERE user_id = :user_id AND session_id = :session_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->bindParam(':session_id', session_id()); // überprüfe session_id() hier
$stmt->execute();
$sessionCheck = $stmt->fetch(PDO::FETCH_ASSOC);

// Debugging: Ausgabe der Session-Abfrage-Ergebnisse
if (!$sessionCheck) {
    var_dump($sessionCheck); // Überprüfe, was in $sessionCheck gespeichert ist
    // Kein Eintrag gefunden -> Der Benutzer ist ausgeloggt, zur Login-Seite umleiten
    header('Location: index.html');
    exit;
}

// Berechtigungen bei jedem Seitenaufruf neu laden
$stmt = $conn->prepare("SELECT role_id FROM users WHERE id = :id");
$stmt->execute([':id' => $_SESSION['user_id']]);
$userRole = $stmt->fetch(PDO::FETCH_ASSOC);

if ($userRole) {
    $roleId = $userRole['role_id'];
    $stmt = $conn->prepare("SELECT permissions FROM roles WHERE id = :role_id");
    $stmt->execute([':role_id' => $roleId]);
    $role = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($role) {
        $permissionsArray = json_decode($role['permissions'], true);
        if (is_array($permissionsArray)) {
            $_SESSION['permissions'] = array_fill_keys($permissionsArray, true);
        } else {
            $_SESSION['permissions'] = [];
        }
    }
}

// Überprüfen, ob der Benutzer Zugang zum Admin-Bereich hat
if (isset($_SESSION['user_id']) && $_SESSION['role'] !== 'admin' || (isset($_SESSION['admin_bereich']) && $_SESSION['admin_bereich'] != 1)) {
    // Wenn der Benutzer kein Admin ist, zur Fehlerseite oder Login-Seite weiterleiten
    header("Location: index.html"); // Weiterleitung zur Login-Seite oder zu einer Fehlerseite
    exit;
}
?>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>LS-Shields | Mitarbeiterverwaltung</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css?v=<?= time(); ?>">
</head>
