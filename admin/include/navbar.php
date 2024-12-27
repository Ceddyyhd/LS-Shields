<?php
session_start();

$user_name = $_SESSION['username'] ?? 'Gast'; // Standardwert, falls keine Session gesetzt ist

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
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
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
                <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?php echo htmlspecialchars($user_name); ?></a>
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
                            <a href="rangverwaltung.php" class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) == 'rangverwaltung.php' ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Rang Verwaltung</p>
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
