<?php
include 'include/db.php';

// Beispiel: Nutzer-ID aus der Session oder URL (z. B. profile.php?id=1)
$user_id = $_GET['id'] ?? 1;

// Benutzerinformationen abrufen
$sql = "SELECT u.email, u.created_at, r.name as role_name FROM users u 
        JOIN roles r ON u.role_id = r.id WHERE u.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    die("Benutzer nicht gefunden.");
}

// Dokumente abrufen
$sql_documents = "SELECT file_name, file_path, uploaded_at FROM documents WHERE user_id = ?";
$stmt_documents = $conn->prepare($sql_documents);
$stmt_documents->bind_param("i", $user_id);
$stmt_documents->execute();
$documents = $stmt_documents->get_result();

// Ausrüstung abrufen
$sql_equipment = "SELECT equipment_name, reveived FROM equipment WHERE user_id = ?";
$stmt_equipment = $conn->prepare($sql_equipment);
$stmt_equipment->bind_param("i", $user_id);
$stmt_equipment->execute();
$equipment = $stmt_equipment->get_result();

// Notizen abrufen
$sql_notes = "SELECT note, created_at FROM notes WHERE user_id = ?";
$stmt_notes = $conn->prepare($sql_notes);
$stmt_notes->bind_param("i", $user_id);
$stmt_notes->execute();
$notes = $stmt_notes->get_result();

// Ausbildungen abrufen
$sql_trainings = "SELECT training_name, rating, completed FROM trainings WHERE user_id = ?";
$stmt_trainings = $conn->prepare($sql_trainings);
$stmt_trainings->bind_param("i", $user_id);
$stmt_trainings->execute();
$trainings = $stmt_trainings->get_result();

// Rechte des Benutzers abrufen
$sql_permissions = "SELECT p.name, p.description, p.display_name FROM permissions p
                    JOIN role_permissions rp ON p.id = rp.permission_id
                    JOIN users u ON rp.role_id = u.role_id WHERE u.id = ?";
$stmt_permissions = $conn->prepare($sql_permissions);
$stmt_permissions->bind_param("i", $user_id);
$stmt_permissions->execute();
$permissions = $stmt_permissions->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'include/header.php'; ?>

<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  <?php include 'include/navbar.php'; ?>

  <!-- Main Sidebar Container -->

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Profile</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">User Profile</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">
            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle" src="dist/img/user4-128x128.jpg" alt="User profile picture">
                </div>

                <h3 class="profile-username text-center">
                  <?php echo htmlspecialchars($user['email']); ?>
                </h3>
                <p class="text-muted text-center">
                  <?php echo htmlspecialchars($user['role_name']); ?>
                </p>

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>Erstellt am:</b> <a class="float-right">
                      <?php echo htmlspecialchars($user['created_at']); ?>
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <div class="col-md-9">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#dokumente" data-toggle="tab">Dokumente</a></li>
                  <li class="nav-item"><a class="nav-link" href="#notizen" data-toggle="tab">Notizen</a></li>
                  <li class="nav-item"><a class="nav-link" href="#ausbildungen" data-toggle="tab">Ausbildungen</a></li>
                  <li class="nav-item"><a class="nav-link" href="#ausruestung" data-toggle="tab">Ausrüstung</a></li>
                  <li class="nav-item"><a class="nav-link" href="#rechte" data-toggle="tab">Rechte</a></li>
                </ul>
              </div>
              <div class="card-body">
                <div class="tab-content">
                  <!-- Dokumente -->
                  <div class="active tab-pane" id="dokumente">
                    <ul>
                      <?php while ($doc = $documents->fetch_assoc()): ?>
                        <li>
                          <a href="<?php echo htmlspecialchars($doc['file_path']); ?>" target="_blank">
                            <?php echo htmlspecialchars($doc['file_name']); ?>
                          </a> (<?php echo htmlspecialchars($doc['uploaded_at']); ?>)
                        </li>
                      <?php endwhile; ?>
                    </ul>
                  </div>

                  <!-- Notizen -->
                  <div class="tab-pane" id="notizen">
                    <ul>
                      <?php while ($note = $notes->fetch_assoc()): ?>
                        <li>
                          <strong><?php echo htmlspecialchars($note['created_at']); ?>:</strong>
                          <?php echo htmlspecialchars($note['note']); ?>
                        </li>
                      <?php endwhile; ?>
                    </ul>
                  </div>

                  <!-- Ausbildungen -->
                  <div class="tab-pane" id="ausbildungen">
                    <ul>
                      <?php while ($training = $trainings->fetch_assoc()): ?>
                        <li>
                          <?php echo htmlspecialchars($training['training_name']); ?>:
                          Bewertung: <?php echo htmlspecialchars($training['rating']); ?>,
                          Abgeschlossen: <?php echo htmlspecialchars($training['completed'] ? 'Ja' : 'Nein'); ?>
                        </li>
                      <?php endwhile; ?>
                    </ul>
                  </div>

                  <!-- Ausrüstung -->
                  <div class="tab-pane" id="ausruestung">
                    <ul>
                      <?php while ($equip = $equipment->fetch_assoc()): ?>
                        <li>
                          <?php echo htmlspecialchars($equip['equipment_name']); ?>:
                          Erhalten: <?php echo htmlspecialchars($equip['reveived']); ?>
                        </li>
                      <?php endwhile; ?>
                    </ul>
                  </div>

                  <!-- Rechte -->
                  <div class="tab-pane" id="rechte">
                    <ul>
                      <?php while ($perm = $permissions->fetch_assoc()): ?>
                        <li>
                          <strong><?php echo htmlspecialchars($perm['display_name']); ?>:</strong>
                          <?php echo htmlspecialchars($perm['description']); ?>
                        </li>
                      <?php endwhile; ?>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 3.2.0
    </div>
    <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
  </footer>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>