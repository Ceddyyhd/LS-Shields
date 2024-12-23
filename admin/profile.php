<?php
include 'include/db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Beispiel: Nutzer-ID aus der Session oder URL (z. B. profile.php?id=1)
$user_id = $_GET['id'] ?? 1;

// Benutzerinformationen abrufen
$sql = "SELECT u.email, u.created_at, r.name as role_name FROM users u 
        JOIN roles r ON u.role_id = r.id WHERE u.id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Benutzer nicht gefunden.");
}

// Dokumente abrufen
$sql_documents = "SELECT file_name, file_path, uploaded_at FROM documents WHERE user_id = :user_id";
$stmt_documents = $conn->prepare($sql_documents);
$stmt_documents->execute(['user_id' => $user_id]);
$documents = $stmt_documents->fetchAll(PDO::FETCH_ASSOC);

// Ausrüstung abrufen
$sql_equipment = "SELECT equipment_name, received FROM equipment WHERE user_id = :user_id";
$stmt_equipment = $conn->prepare($sql_equipment);
$stmt_equipment->execute(['user_id' => $user_id]);
$equipment = $stmt_equipment->fetchAll(PDO::FETCH_ASSOC);

// Notizen abrufen
$sql_notes = "SELECT note, created_at FROM notes WHERE user_id = :user_id";
$stmt_notes = $conn->prepare($sql_notes);
$stmt_notes->execute(['user_id' => $user_id]);
$notes = $stmt_notes->fetchAll(PDO::FETCH_ASSOC);

// Ausbildungen abrufen
$sql_trainings = "SELECT training_name, rating, completed FROM trainings WHERE user_id = :user_id";
$stmt_trainings = $conn->prepare($sql_trainings);
$stmt_trainings->execute(['user_id' => $user_id]);
$trainings = $stmt_trainings->fetchAll(PDO::FETCH_ASSOC);

// Rechte des Benutzers abrufen
$sql_permissions = "SELECT p.name, p.description, p.display_name 
                    FROM permissions p
                    JOIN roles r ON r.id = :role_id";
$stmt_permissions = $conn->prepare($sql_permissions);
$stmt_permissions->execute(['role_id' => $user['role_id']]);
$permissions = $stmt_permissions->fetchAll(PDO::FETCH_ASSOC);
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
                  <div class="tab-pane active" id="dokumente">
    <form action="upload_document.php" method="POST" enctype="multipart/form-data">
        <div class="form-group row">
            <label for="exampleInputFile" class="col-sm-2 col-form-label">Datei hochladen</label>
            <div class="col-sm-10">
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="exampleInputFile" name="document" required>
                        <label class="custom-file-label" for="exampleInputFile">Wähle eine Datei</label>
                    </div>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </div>
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id); ?>">
            </div>
        </div>
    </form>
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
                          Erhalten: <?php echo htmlspecialchars($equip['received']); ?>
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