<?php
include 'include/db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Beispiel: Nutzer-ID aus der Session oder URL (z. B. profile.php?id=1)
$user_id = $_GET['id'] ?? null;
if (!$user_id) {
    die("Benutzer-ID fehlt.");
}
// Benutzerinformationen abrufen
$sql = "SELECT users.*, roles.name AS role_name 
        FROM users 
        LEFT JOIN roles ON users.role_id = roles.id 
        WHERE users.id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute(['id' => $user_id]);
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
                  <?php echo htmlspecialchars($user['name']); ?>
                </h3>
                <p class="text-muted text-center">
                  <?php echo htmlspecialchars($user['role_name']); ?>
                </p>

                <ul class="list-group list-group-unbordered mb-3">
                <li class="list-group-item">
                <b>Tel. Nr.:</b> <a class="float-right"><?php echo htmlspecialchars($user['nummer']); ?></a>
              </li>
                  <li class="list-group-item">
                    <b>Erstellt am:</b> <a class="float-right">
                      <?php echo htmlspecialchars($user['created_at']); ?>
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          <!-- About Me Box -->
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Information</h3>
          </div>
          <div class="card-body">
            <strong><i class="fas fa-envelope mr-1"></i> Email</strong>
            <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>

            <hr>
            <strong><i class="fas fa-envelope mr-1"></i> UMail</strong>
            <p class="text-muted"><?php echo htmlspecialchars($user['umail']); ?></p>

            <hr>
            <strong><i class="fas fa-credit-card mr-1"></i> Kontonummer</strong>
            <p class="text-muted"><?php echo htmlspecialchars($user['kontonummer']); ?></p>

            <hr>
            <strong><i class="far fa-file-alt mr-1"></i> Letzte Beförderung durch</strong>
            <p class="text-muted">Kane</p>
          </div>
          <button type="button" class="btn btn-block btn-primary">Speichern</button>

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
    <form class="form-horizontal" action="include/upload_document.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id); ?>">
        <!-- Waffenschein -->
        <div class="form-group row">
            <label for="waffenscheinSelect" class="col-sm-2 col-form-label">Waffenschein</label>
            <div class="form-group d-flex align-items-center" style="flex-wrap: nowrap;">
                <div style="margin-right: 20px; width: 200px;">
                    <select id="waffenscheinSelect" class="form-control" style="height: 38px; width: 100%;" name="waffenschein_type">
                        <option value="none">Keiner Vorhanden</option>
                        <option value="small">Kleiner Waffenschein</option>
                        <option value="big_small">Großer & Kleiner Waffenschein</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Führerscheine -->
        <div class="form-group row">
            <label for="fuehrerscheinSelect" class="col-sm-2 col-form-label">Führerscheine</label>
            <div class="form-group d-flex align-items-center" style="flex-wrap: nowrap;">
                <div style="margin-right: 20px; width: 200px;">
                    <select id="fuehrerscheinSelect" class="form-control" multiple name="fuehrerscheine[]" style="height: 100px; width: 100%;">
                        <option value="C">C</option>
                        <option value="A">A</option>
                        <option value="M2">M2</option>
                        <option value="PTL">PTL</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Weitere Dokumente -->
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-upload">
    </form>

    <!-- Liste der hochgeladenen Dokumente -->
    <div class="mt-4">
        <h5>Hochgeladene Dokumente:</h5>
        <ul>
            <?php if (!empty($documents)): ?>
                <?php foreach ($documents as $doc): ?>
                    <li>
                        <a href="<?= htmlspecialchars($doc['file_path']); ?>" target="_blank">
                            <?= htmlspecialchars($doc['file_name']); ?>
                        </a> (<?= htmlspecialchars($doc['uploaded_at']); ?>)
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>Keine Dokumente vorhanden.</li>
            <?php endif; ?>
        </ul>
    </div>
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