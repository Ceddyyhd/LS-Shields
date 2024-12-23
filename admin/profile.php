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
$sql_notes = "SELECT type, content, created_at, author FROM notes WHERE user_id = :user_id ORDER BY created_at DESC";
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
          <button type="button" id="saveButton" class="btn btn-block btn-primary">Speichern</button>

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
                  <form id="employeeForm" class="form-horizontal" method="POST">
    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id); ?>">
    <?php
// Führerscheine aus der Datenbank abrufen
$sql = "SELECT fuehrerscheine FROM employee_info WHERE user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$fuehrerscheine = json_decode($result['fuehrerscheine'] ?? '[]', true);

if (!is_array($fuehrerscheine)) {
    $fuehrerscheine = []; // Falls JSON fehlerhaft ist, Standardwert setzen
}

// Waffenschein aus der Datenbank abrufen
$sql = "SELECT waffenschein_type FROM employee_info WHERE user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$waffenschein_type = $result['waffenschein_type'] ?? 'none'; // Standardwert 'none', falls nicht gesetzt

?>
    <!-- Waffenschein -->
    <div class="form-group row">
    <label for="waffenscheinSelect" class="col-sm-2 col-form-label">Waffenschein</label>
    <div class="col-sm-10">
        <select id="waffenscheinSelect" class="form-control" name="waffenschein_type">
            <option value="none" <?= $waffenschein_type === 'none' ? 'selected' : ''; ?>>Keiner Vorhanden</option>
            <option value="small" <?= $waffenschein_type === 'small' ? 'selected' : ''; ?>>Kleiner Waffenschein</option>
            <option value="big_small" <?= $waffenschein_type === 'big_small' ? 'selected' : ''; ?>>Großer & Kleiner Waffenschein</option>
        </select>
    </div>
</div>



<!-- Führerscheine -->
<div class="form-group row">
    <label for="fuehrerscheinSelect" class="col-sm-2 col-form-label">Führerscheine</label>
    <div class="col-sm-10">
    <select id="fuehrerscheinSelect" class="form-control" multiple name="fuehrerscheine[]">
    <option value="C" <?= in_array('C', $fuehrerscheine) ? 'selected' : ''; ?>>C</option>
    <option value="A" <?= in_array('A', $fuehrerscheine) ? 'selected' : ''; ?>>A</option>
    <option value="M2" <?= in_array('M2', $fuehrerscheine) ? 'selected' : ''; ?>>M2</option>
    <option value="PTL" <?= in_array('PTL', $fuehrerscheine) ? 'selected' : ''; ?>>PTL</option>
</select>
    </div>
</div>

        <!-- Weitere Dokumente -->
        <script>
$(document).ready(function () {
    // Speichern-Button
    $("#saveButton").on("click", function () {
    var formData = $("#employeeForm").serialize();

    console.log("Gesendete Daten:", formData); // Debugging

    $.ajax({
        url: "include/save_employee_info.php",
        type: "POST",
        data: formData,
        success: function (response) {
            console.log("Antwort:", response); // Debugging
            if (response.success) {
                alert(response.message);
            } else {
                alert("Fehler: " + response.message);
            }
        },
        error: function (xhr, status, error) {
            console.error("Fehler:", error);
            alert("Fehler: " + error);
        }
    });
});
    // Datei-Auswahl anzeigen
    $("#documentFile").on("change", function () {
        var fileName = $(this).val().split("\\").pop();
        $(this).next(".custom-file-label").text(fileName);
    });
});
</script>        
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
            <div class="modal-header">
                <h4 class="modal-title">
                    <?php if ($_SESSION['permissions']['upload_file'] ?? false): ?>
                        Datei hochladen
                    <?php else: ?>
                        Keine Berechtigung
                    <?php endif; ?>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <?php if ($_SESSION['permissions']['upload_file'] ?? false): ?>
                <!-- Formular nur anzeigen, wenn Berechtigung vorhanden -->
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
            <?php else: ?>
                <!-- Nachricht für Benutzer ohne Berechtigung -->
                <div class="modal-body">
                    <p>Sie haben keine Berechtigung, Dateien hochzuladen.</p>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
                </div>
            <?php endif; ?>
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
    <?php if ($_SESSION['permissions']['view_documents'] ?? false): ?>
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
    <?php else: ?>
        <p>Sie haben keine Berechtigung, die hochgeladenen Dokumente anzuzeigen.</p>
    <?php endif; ?>
</div>

</div>

<!-- Tab für Notizen -->
<div class="tab-pane" id="notizen">
    <?php if ($_SESSION['permissions']['create_note'] ?? false || $_SESSION['permissions']['create_warning'] ?? false || $_SESSION['permissions']['create_termination'] ?? false): ?>
        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#modal-default">Notiz hinzufügen</button>
    <?php else: ?>
        <p class="text-muted">Sie haben keine Berechtigung, Notizen hinzuzufügen.</p>
    <?php endif; ?>

    <div id="timeline" class="timeline timeline-inverse">
        <?php foreach ($notes as $note): ?>
            <div>
                <?php
                $iconClass = 'fas fa-user bg-secondary';
                if ($note['type'] === 'notiz') {
                    $iconClass = 'fas fa-user bg-info';
                } elseif ($note['type'] === 'verwarnung') {
                    $iconClass = 'fas fa-user bg-warning';
                } elseif ($note['type'] === 'kuendigung') {
                    $iconClass = 'fas fa-user bg-danger';
                }
                ?>
                <i class="<?= $iconClass; ?>"></i>
                <div class="timeline-item">
                    <span class="time"><i class="far fa-clock"></i> <?= htmlspecialchars($note['created_at']); ?></span>
                    <h3 class="timeline-header"><?= htmlspecialchars($note['author'] ?? 'Unbekannt'); ?> fügte eine <?= htmlspecialchars($note['type']); ?> hinzu</h3>
                    <div class="timeline-body"><?= htmlspecialchars($note['content']); ?></div>
                </div>
            </div>
        <?php endforeach; ?>
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
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id); ?>"> <!-- Benutzer-ID -->
                <div class="modal-body">
                    <div class="form-group">
                        <label>Typ</label>
                        <select id="noteType" name="note_type" class="form-control" required>
                            <?php if ($_SESSION['permissions']['create_note'] ?? false): ?>
                                <option value="notiz">Notiz</option>
                            <?php endif; ?>
                            <?php if ($_SESSION['permissions']['create_warning'] ?? false): ?>
                                <option value="verwarnung">Verwarnung</option>
                            <?php endif; ?>
                            <?php if ($_SESSION['permissions']['create_termination'] ?? false): ?>
                                <option value="kuendigung">Kündigung</option>
                            <?php endif; ?>
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

<script>$("#noteForm").on("submit", function (e) {
    e.preventDefault();
    var formData = $(this).serialize();

    console.log("Form Data:", formData); // Debugging

    $.ajax({
        url: "include/add_note.php",
        type: "POST",
        data: formData,
        success: function (response) {
            console.log("Response:", response); // Debugging
            if (response.success) {
                $("#modal-default").modal("hide");
                $("#noteForm")[0].reset();

                var iconClass;
                switch (response.data.type) {
                    case "notiz":
                        iconClass = "fas fa-user bg-info";
                        break;
                    case "verwarnung":
                        iconClass = "fas fa-user bg-warning";
                        break;
                    case "kuendigung":
                        iconClass = "fas fa-user bg-danger";
                        break;
                }

                var newNote = `
                  <div>
                      <i class="${iconClass}"></i>
                      <div class="timeline-item">
                          <span class="time"><i class="far fa-clock"></i> ${response.data.created_at}</span>
                          <h3 class="timeline-header">${response.data.user} fügte eine ${response.data.type} hinzu</h3>
                          <div class="timeline-body">${response.data.content}</div>
                      </div>
                  </div>`;
              $("#timeline").prepend(newNote);
            } else {
                alert(response.message);
            }
        },
        error: function (xhr, status, error) {
            console.error("Fehler:", error);
            alert("Fehler: " + error);
        },
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