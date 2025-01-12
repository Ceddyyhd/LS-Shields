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
        <?php
// Routing-Tabelle mit Unterbereichen
$routes = [
    'dashboard' => [
        'label' => 'Mitarbeiter Bereich',
        'path' => 'index.php',
        'subroutes' => [
            'dashboard' => ['label' => 'Dashboard', 'path' => 'index.php'],
            'eventplanung' => ['label' => 'Eventplanung', 'path' => 'eventplanung.php'],
            'training' => ['label' => 'Trainings Kalender', 'path' => 'training.php'],
            'anfragen' => ['label' => 'Anfragen', 'path' => 'anfragen.php'],
            'calendar' => ['label' => 'Kalender', 'path' => 'calendar.php'],
            'kundenverwaltung' => ['label' => 'Kunden Verwaltung', 'path' => 'kundenverwaltung.php'],
            'verbesserungen' => ['label' => 'Verbesserungsvorschläge', 'path' => 'verbesserungen.php'],
            'fahrzeugverwaltung' => ['label' => 'Fahrzeugverwaltung', 'path' => 'fahrzeugverwaltung.php'],
        ]
    ],
    'admin_area' => [
        'label' => 'Leitungs Bereich',
        'path' => '#', // Kein Direktlink, da Unterbereiche vorhanden sind
        'subroutes' => [
            'mitarbeiterverwaltung' => ['label' => 'Mitarbeiter Verwaltung', 'path' => 'mitarbeiterverwaltung.php'],
            'rangverwaltung' => ['label' => 'Rang Verwaltung', 'path' => 'rangverwaltung.php'],
            'finanzverwaltung' => ['label' => 'Finanzverwaltung', 'path' => 'finanzverwaltung.php'],
            'urlaubsverwaltung' => ['label' => 'Urlaubsverwaltung', 'path' => 'urlaubsverwaltung.php'],
        ]
    ],
    'settings' => [
        'label' => 'Settings',
        'path' => '#', // Kein Direktlink, da Unterbereiche vorhanden sind
        'subroutes' => [
            'ausbildungen' => ['label' => 'Ausbildung Verwaltung', 'path' => 'ausbildungen.php'],
            'ausruestung' => ['label' => 'Ausrüstung Verwaltung', 'path' => 'ausruestung.php'],
            'ankuendigung' => ['label' => 'Ankündigungen', 'path' => 'ankuendigung.php'],
        ]
    ]
];

// Aktuelle Seite ermitteln
$current_page = basename($_SERVER['SCRIPT_NAME']);
?>
        <!-- Sidebar Menu -->
        <nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Dynamische Links basierend auf der Routing-Tabelle -->
        <?php foreach ($routes as $route_key => $route): ?>
            <li class="nav-item <?= isset($route['subroutes']) ? 'menu-open' : '' ?>">
                <a href="<?= $route['path'] ?>" class="nav-link <?= ($current_page == basename($route['path'])) ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>
                        <?= $route['label'] ?>
                        <?= isset($route['subroutes']) ? '<i class="right fas fa-angle-left"></i>' : '' ?>
                    </p>
                </a>
                <!-- Untermenü -->
                <?php if (isset($route['subroutes'])): ?>
                    <ul class="nav nav-treeview">
                        <?php foreach ($route['subroutes'] as $subroute_key => $subroute): ?>
                            <li class="nav-item">
                                <a href="<?= $subroute['path'] ?>" class="nav-link <?= ($current_page == basename($subroute['path'])) ? 'active' : '' ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p><?= $subroute['label'] ?></p>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
