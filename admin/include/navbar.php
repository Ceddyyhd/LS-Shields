<?php
include 'security_check.php'; // Sicherheitsprüfung für diese Datei

session_start();

session_regenerate_id(true);

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include 'include/db.php';
include 'auth.php'; // Authentifizierungslogik einbinden

// Session-Wiederherstellung prüfen
restoreSessionIfRememberMe($conn);

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

$user_name = $_SESSION['username'] ?? 'Gast'; // Standardwert, falls keine Session gesetzt ist
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

$query = "SELECT setting_value FROM admin_settings WHERE setting_key = 'maintenance_mode'";
$stmt = $conn->prepare($query);
$stmt->execute();
$maintenance = $stmt->fetch(PDO::FETCH_ASSOC);

// Wenn der Wartungsmodus aktiviert ist und der Benutzer keine Berechtigung hat, wird er weitergeleitet
if ($maintenance['setting_value'] === 'on' && !($_SESSION['permissions']['access_maintenance'] ?? false)) {
    // Benutzer ohne Berechtigung auf Wartungsseite umleiten
    header('Location: maintenance.php');
    exit;
}

// Überprüfen, ob der Benutzer in der `user_sessions`-Tabelle eingetragen ist (für Mitarbeiter)
$query = "SELECT * FROM user_sessions WHERE user_id = :user_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$sessionCheck = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$sessionCheck) {
    // Kein Eintrag für diese Benutzer-ID gefunden -> Umleitung zur Login-Seite
    header('Location: index.html');
    exit;
}

// Überprüfen, ob der Benutzer ein Admin ist und ob eine Force-Logout-Anfrage vorliegt
if (isset($_GET['force_logout_user_id']) && $_SESSION['role'] === 'admin') {
    $user_id_to_logout = $_GET['force_logout_user_id'];

    // Das 'remember_token' des Benutzers löschen
    $query = "UPDATE users SET remember_token = NULL WHERE id = :user_id";
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

// Benutzerinformationen abrufen
$sql = "SELECT users.*, roles.name AS role_name, users.profile_image 
            FROM users 
            LEFT JOIN roles ON users.role_id = roles.id 
            WHERE users.id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>


<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light dark-mode">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="include/logout.php" role="button">
          <i class="fas fa-right-from-bracket"></i>
        </a>
      </li>
    </ul>
</nav>
<!-- /.navbar -->

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
      <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">AdminLTE 3</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <?php  $profileImage = $_SESSION['profile_image']; ?>
                <img src="<?php echo htmlspecialchars($profileImage); ?>" class="img-circle elevation-2" alt="User Image">
                </div>
            <div class="info">
            <?php  $user_id1 = $_SESSION['user_id']; ?>
                <a href="profile.php?id=<?php echo htmlspecialchars($user_id1); ?>" class="d-block"><?php echo htmlspecialchars($user_name); ?></a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Mitarbeiter Bereich -->
                <li class="nav-item menu-open">
                    <a href="#" class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) == 'index.php' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Mitarbeiter Bereich
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="index.php" class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) == 'index.php' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="eventplanung.php" class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) == 'eventplanung.php' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Eventplanung</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="training.php" class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) == 'training.php' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Trainings Kalender</p>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="anfragen.php" class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) == 'anfragen.php' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Anfragen</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="calendar.php" class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) == 'calendar.php' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Kalender</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="kundenverwaltung.php" class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) == 'kundenverwaltung.php' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Kunden Verwaltung</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="verbesserungen.php" class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) == 'verbesserungen.php' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Verbesserungsvorschlag</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="fahrzeugverwaltung.php" class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) == 'fahrzeugverwaltung.php' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Fahrzeugverwaltung</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Leitungs Bereich -->
                <li class="nav-item menu-open">
                    <a href="#" class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) == 'leitungsbereich.php' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Leitungs Bereich
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="mitarbeiterverwaltung.php" class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) == 'mitarbeiterverwaltung.php' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Mitarbeiter Verwaltung</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="rangverwaltung.php" class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) == 'rangverwaltung.php' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Rang Verwaltung</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="finanzverwaltung.php" class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) == 'finanzverwaltung.php' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Finanzverwaltung</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="urlaubsverwaltung.php" class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) == 'urlaubsverwaltung.php' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Urlaubsverwaltung</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item menu-open">
                    <a href="#" class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) == 'leitungsbereich.php' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Settings
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="ausbildungen.php" class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) == 'ausbildungen.php' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Ausbildung Verwaltung</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="ausruestung.php" class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) == 'ausruestung.php' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Ausrüstung Verwaltung</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="ankündigung.php" class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) == 'ankündigung.php' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Ankündigungen</p>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
