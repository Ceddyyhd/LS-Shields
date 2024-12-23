<?php
include 'include/db.php';
ini_set('display_errors', 0);
error_reporting(0);
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
<!-- jQuery (notwendig für Bootstrap) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

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

        <!-- Button für das Modal -->
          <div class="form-group row">
              <label for="uploadButton" class="col-sm-2 col-form-label">Dokumente Hochladen</label>
              <div class="col-sm-10">
                  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-primary">
                      Dokument hochladen
                  </button>
              </div>
          </div>
    </form>

<!-- Modal für Dateiupload -->
<div class="modal fade" id="modal-primary">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h4 class="modal-title">Datei hochladen</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="uploadForm" action="include/upload_document.php" method="POST" enctype="multipart/form-data">
    <!-- Benutzer-ID -->
    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id); ?>">
    <!-- Dokumenttyp -->
    <input type="hidden" name="doc_type" value="arbeitsvertrag"> <!-- Beispiel für den Dokumenttyp -->

    <div class="modal-body">
        <!-- Eingabe für den benutzerdefinierten Namen -->
        <div class="form-group">
            <label for="documentName">Dokumentname</label>
            <input type="text" id="documentName" name="document_name" class="form-control" placeholder="z.B. Arbeitsvertrag" required>
        </div>

        <!-- Dateiupload -->
        <div class="form-group">
            <label for="documentFile">Datei auswählen</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="documentFile" name="document_file" required>
                <label class="custom-file-label" for="documentFile">Datei auswählen</label>
            </div>
        </div>
    </div>

    <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
        <button type="submit" class="btn btn-primary">Hochladen</button>
    </div>
</form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<script>
    document.getElementById("documentFile").addEventListener("change", function() {
        var fileName = this.files[0].name;
        var nextSibling = this.nextElementSibling;
        nextSibling.innerText = fileName; // Ändert den Text des Labels
    });
</script>
<script>
    $(document).ready(function() {
    $("#uploadForm").on("submit", function(e) {
        e.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            url: $(this).attr("action"),
            type: $(this).attr("method"),
            data: formData,
            processData: false, // Wichtig für FormData
            contentType: false, // Wichtig für FormData
            success: function(response) {
                $("#modal-primary").modal("hide"); // Modal schließen
                location.reload(); // Seite neu laden, um Änderungen zu sehen
            },
            error: function(xhr, status, error) {
            }
        });
    });
});
</script>


<!-- /.modal -->

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
    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#modal-default">Notiz hinzufügen</button>
    <div id="timeline" class="timeline timeline-inverse">
        <!-- Timeline-Notizen werden hier dynamisch eingefügt -->
    </div>
</div>

<!-- Modal für Notizerstellung -->
<div class="modal fade" id="modal-default">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Notiz erstellen</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="noteForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Typ</label>
                        <select id="noteType" name="note_type" class="form-control" required>
                            <option value="notiz">Notiz</option>
                            <option value="verwarnung">Verwarnung</option>
                            <option value="kuendigung">Kündigung</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Inhalt</label>
                        <textarea id="noteContent" name="note_content" class="form-control" rows="3" placeholder="Enter ..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Abbrechen</button>
                    <button type="submit" class="btn btn-primary">Speichern</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Notiz erstellen
    $("#noteForm").on("submit", function(e) {
        e.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            url: "include/add_note.php", // Server-Skript zum Speichern der Notiz
            type: "POST",
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Modal schließen
                    $("#modal-default").modal("hide");

                    // Timeline aktualisieren
                    var newNote = `
                        <div>
                            <i class="fas fa-comments bg-warning"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="far fa-clock"></i> ${response.data.created_at}</span>
                                <h3 class="timeline-header">${response.data.user} fügte eine Notiz hinzu</h3>
                                <div class="timeline-body">${response.data.content}</div>
                            </div>
                        </div>`;
                    $("#timeline").prepend(newNote);
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                alert("Fehler: " + error);
            }
        });
    });
});
</script>

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