<?php
session_start();

// Session-Regeneration sicherstellen (gibt dem Benutzer eine neue Session-ID für mehr Sicherheit)
session_regenerate_id(true);

// Cache-Control-Header setzen, um die Seite nicht zu cachen
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Datenbankverbindung einbinden
include 'include/db.php';
include 'auth.php';  // Authentifizierungslogik einbinden

// Überprüfen, ob der Benutzer eingeloggt ist, wenn nicht, zur Login-Seite weiterleiten
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
        header('Location: index.html');  // Umleitung zur Login-Seite
        exit;
    }
}

// Überprüfen, ob der Benutzer in der `kunden_sessions`-Tabelle eingetragen ist
$query = "SELECT * FROM kunden_sessions WHERE user_id = :user_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$sessionCheck = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$sessionCheck) {
    // Sitzung ist ungültig -> zur Login-Seite umleiten
    header('Location: index.html');
    exit;
}

// Überprüfen, ob der Benutzer ein Admin ist und ob eine Force-Logout-Anfrage vorliegt
if (isset($_GET['force_logout_user_id']) && $_SESSION['role'] === 'admin') {
    $user_id_to_logout = $_GET['force_logout_user_id'];

    // Das 'remember_token' des Benutzers löschen
    $query = "UPDATE kunden SET remember_token = NULL WHERE id = :user_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id_to_logout);
    $stmt->execute();

    // Lösche das 'remember_me' Cookie, falls gesetzt
    setcookie('remember_me', '', time() - 3600, '/');

    // Falls der geloggte Benutzer derselbe ist, auch seine Session zerstören
    if ($_SESSION['user_id'] == $user_id_to_logout) {
        session_unset();
        session_destroy();
        setcookie('PHPSESSID', '', time() - 3600, '/');  // Löscht das PHP-Session-Cookie
        header('Location: index.html');  // Weiterleitung zur Login-Seite
        exit;
    }
    
    echo json_encode(['success' => true, 'message' => 'Benutzer wurde erfolgreich abgemeldet.']);
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
