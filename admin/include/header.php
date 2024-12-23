<?php
session_start();
include 'include/db.php';

// Prüfen, ob der Benutzer eingeloggt ist
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

            // Berechtigungen laden
            $stmt = $conn->prepare("SELECT role_id FROM users WHERE id = :id");
            $stmt->execute([':id' => $user['id']]);
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
                        $_SESSION['permissions'] = []; // Fallback, falls keine Berechtigungen vorliegen
                    }
                }
            }
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
} else {
    // Berechtigungen laden, falls noch nicht gesetzt
    if (!isset($_SESSION['permissions'])) {
        $stmt = $conn->prepare("SELECT role_id FROM users WHERE id = :id");
        $stmt->execute([':id' => $_SESSION['user_id']]);
        $userRole = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userRole) {
            $roleId = $userRole['role_id'];
            $stmt = $conn->prepare("SELECT permissions FROM roles WHERE id = :role_id");
            $stmt->execute([':role_id' => $roleId]);
            $role = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($role) {
                $_SESSION['permissions'] = json_decode($role['permissions'], true);
            }
        }
    }
}

// Debugging (entfernen in der Produktion)
if (isset($_SESSION['permissions'])) {
    error_log("Permissions: " . print_r($_SESSION['permissions'], true));
} else {
    error_log("Permissions not set.");
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
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
