<?php
include 'include/db.php';
ini_set('display_errors', 0);
error_reporting(0);

// Beispiel: Kunden-ID aus der Session oder URL (z. B. profile.php?id=1)
$customer_id = $_GET['id'] ?? null;
if (!$customer_id) {
    die("Kunden-ID fehlt.");
}

// Kundeninformationen abrufen
$sql = "SELECT k.* 
        FROM kunden k
        WHERE k.id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute(['id' => $customer_id]);
$kunden = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kunden) {
    die("Kunde nicht gefunden.");
}

// Dokumente für den Kunden abrufen
$sql_documents = "SELECT file_name, file_path, uploaded_at FROM documents_customer WHERE user_id = :user_id";
$stmt_documents = $conn->prepare($sql_documents);
$stmt_documents->execute(['user_id' => $customer_id]);
$documents = $stmt_documents->fetchAll(PDO::FETCH_ASSOC);

// Notizen abrufen
$sql_notes = "SELECT type, content, created_at, author FROM notes_customer WHERE user_id = :user_id ORDER BY created_at DESC";
$stmt_notes = $conn->prepare($sql_notes);
$stmt_notes->execute(['user_id' => $customer_id]);
$notes = $stmt_notes->fetchAll(PDO::FETCH_ASSOC);

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
                  <img class="profile-user-img img-fluid img-circle" src="<?php echo htmlspecialchars($kunden['profile_image']); ?>" alt="User profile picture">
                </div>

                <h3 class="profile-username text-center">
                  <?php echo htmlspecialchars($kunden['name']); ?>
                </h3>








                <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
                </a>
                </p>

                <ul class="list-group list-group-unbordered mb-3">
                <li class="list-group-item">
                <b>Tel. Nr.:</b> <a class="float-right"><?php echo htmlspecialchars($kunden['nummer']); ?></a>
              </li>
                  <li class="list-group-item">
                    <b>Erstellt am:</b> <a class="float-right">
                      <?php echo htmlspecialchars($kunden['created_at']); ?>
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
            <strong><i class="fas fa-envelope mr-1"></i> UMail</strong>
            <p class="text-muted"><?php echo htmlspecialchars($kunden['umail']); ?></p>

            <hr>
            <strong><i class="fas fa-credit-card mr-1"></i> Kontonummer</strong>
            <p class="text-muted"><?php echo htmlspecialchars($kunden['kontonummer']); ?></p>
          </div>
          
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#user-bearbeiten">
                  Kunden Bearbeiten
                </button>
          <button type="button" id="saveButton" class="btn btn-block btn-primary">Speichern</button>
        </div>
      </div>
      <form id="userEditForm">
    <input type="hidden" name="user_id" value="<?= htmlspecialchars($kunden['id']); ?>">
    <div class="modal fade" id="user-bearbeiten">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Benutzer bearbeiten</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Information</h3>
                        </div>
                        <div class="card-body">

                            <!-- Name -->
                            <div class="form-group">
                                <strong><i class="fas fa-user mr-1"></i> Name</strong>
                                <?php if ($_SESSION['permissions']['edit_name'] ?? false): ?>
                                    <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($kunden['name']); ?>">
                                <?php else: ?>
                                    <input type="text" class="form-control" placeholder="<?php echo htmlspecialchars($kunden['name']); ?>" disabled>
                                <?php endif; ?>
                            </div>

                            <!-- Nummer -->
                            <div class="form-group">
                                <strong><i class="fas fa-phone mr-1"></i> Nummer</strong>
                                <?php if ($_SESSION['permissions']['edit_nummer'] ?? false): ?>
                                    <input type="text" class="form-control" name="nummer" value="<?php echo htmlspecialchars($kunden['nummer']); ?>">
                                <?php else: ?>
                                    <input type="text" class="form-control" placeholder="<?php echo htmlspecialchars($kunden['nummer']); ?>" disabled>
                                <?php endif; ?>
                            </div>

                            <!-- UMail -->
                            <div class="form-group">
                                <strong><i class="fas fa-envelope mr-1"></i> UMail</strong>
                                <?php if ($_SESSION['permissions']['edit_umail'] ?? false): ?>
                                    <input type="text" class="form-control" name="umail" value="<?php echo htmlspecialchars($kunden['umail']); ?>">
                                <?php else: ?>
                                    <input type="text" class="form-control" placeholder="<?php echo htmlspecialchars($kunden['umail']); ?>" disabled>
                                <?php endif; ?>
                            </div>

                            <!-- Kontonummer -->
                            <div class="form-group">
                                <strong><i class="fas fa-credit-card mr-1"></i> Kontonummer</strong>
                                <?php if ($_SESSION['permissions']['edit_kontonummer'] ?? false): ?>
                                    <input type="text" class="form-control" name="kontonummer" value="<?php echo htmlspecialchars($kunden['kontonummer']); ?>">
                                <?php else: ?>
                                    <input type="text" class="form-control" placeholder="<?php echo htmlspecialchars($kunden['kontonummer']); ?>" disabled>
                                <?php endif; ?>
                            </div>
                            <!-- Passwort ändern -->
                            <div class="form-group">
                                <strong><i class="fas fa-lock mr-1"></i> Passwort ändern</strong>
                                <div class="form-check">
                                    <input type="checkbox" id="changePasswordCheckbox" class="form-check-input">
                                    <label for="changePasswordCheckbox" class="form-check-label">Passwort ändern</label>
                                </div>
                                <input type="password" id="passwordField" name="password" class="form-control" placeholder="Neues Passwort" disabled>
                            </div>
                            <!-- Gekündigt -->
                            <div class="form-group">
    <strong><i class="fas fa-user-times mr-1"></i> Gelöscht</strong> 
    <div class="form-check">
    <input type="checkbox" id="gekuendigtCheckbox" class="form-check-input" name="gekuendigt" <?php echo $kunden['gekuendigt'] === 'gekuendigt' ? 'checked' : ''; ?>>
    <label for="gekuendigtCheckbox" class="form-check-label">Kunde als Gelöscht markieren</label>
    </div>
</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
                    <button type="submit" class="btn btn-primary" id="saveChanges">Speichern</button>
                </div>
            </div>
        </div>
    </div>
</form>


<script>
    // Event-Listener für die Checkbox
    document.getElementById('changePasswordCheckbox').addEventListener('change', function() {
        // Hole das Passwortfeld
        var passwordField = document.getElementById('passwordField');
        
        // Wenn die Checkbox aktiviert ist, setze 'disabled' auf false (also aktivieren)
        if (this.checked) {
            passwordField.disabled = false;
        } else {
            // Wenn die Checkbox deaktiviert ist, setze 'disabled' auf true (also deaktivieren)
            passwordField.disabled = true;
        }
    });
    $(document).ready(function () {
        $("#saveChanges").on("click", function (e) {
            e.preventDefault();

            // Sammle die Daten aus dem Formular
            var formData = $("#userEditForm").serialize();

            // Sende die Daten per AJAX
            $.ajax({
                url: "include/edit_customer.php",
                type: "POST",
                data: formData,
                success: function(response) {
                    try {
                        // Versuche, die Antwort als JSON zu parsen
                        response = JSON.parse(response);  // Hier die Antwort als JSON parsen

                        if (response.success) {
                            alert('Daten erfolgreich gespeichert');
                            $('#user-bearbeiten').modal('hide'); // Schließt das Modal
                            location.reload();  // Lädt die Seite neu
                        } else {
                            alert('Fehler: ' + response.message);  // Fehlernachricht anzeigen
                        }
                    } catch (error) {
                        console.error("Fehler beim Parsen der Antwort:", error);
                        alert("Fehler beim Parsen der Antwort");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX-Fehler:", error);
                }
            });
        });
    });
</script>

          <div class="col-md-9">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#dokumente" data-toggle="tab">Dokumente</a></li>
                  <li class="nav-item"><a class="nav-link" href="#notizen" data-toggle="tab">Notizen</a></li>
                  <li class="nav-item"><a class="nav-link" href="#rechnungen" data-toggle="tab">Rechnungen</a></li>

                </ul>
              </div>
              <div class="card-body">
                <div class="tab-content">
                  <!-- Dokumente -->
                  <div class="active tab-pane" id="dokumente">
                  <form id="employeeForm" class="form-horizontal" method="POST">
    <input type="hidden" name="user_id" value="<?= htmlspecialchars($customer_id); ?>">
    
        <!-- Weitere Dokumente -->
        <script>
$(document).ready(function () {
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
                <form id="uploadForm" action="include/upload_document_customer.php" method="POST" enctype="multipart/form-data">
                    <!-- Benutzer-ID -->
                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($customer_id); ?>">
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
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($customer_id); ?>"> <!-- Kunden-ID -->
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

<script>
$("#noteForm").on("submit", function (e) {
    e.preventDefault();
    var formData = $(this).serialize();

    $.ajax({
        url: "include/add_note_customer.php",
        type: "POST",
        data: formData,
        success: function (response) {
            try {
                response = JSON.parse(response);

                if (response.success) {
                  $('#modal-default').modal('hide');
                  $('.modal-backdrop').remove(); // Entfernt den dunklen Hintergrund
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
                        default:
                            iconClass = "fas fa-user bg-secondary";
                            break;
                    }

                    var newNote = ` 
                        <div>
                            <i class="${iconClass}"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="far fa-clock"></i> ${response.data.created_at}</span>
                                <h3 class="timeline-header">${response.data.author} fügte eine ${response.data.type} hinzu</h3>
                                <div class="timeline-body">${response.data.content}</div>
                            </div>
                        </div>`;
                    $("#timeline").prepend(newNote);
                } else {
                    alert(response.message);
                }
            } catch (error) {
                console.error("Fehler beim Parsen der Antwort:", error);
            }
        },
        error: function (xhr, status, error) {
            console.error("Fehler:", error);
            alert("Es ist ein Fehler aufgetreten: " + error);
        },
    });
});
</script>


<div class="tab-pane" id="rechnungen">
    

            <div class="card-body">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th style="width: 10px">#</th>
                      <th>Rechnung</th>
                      <th>Status</th>
                      <th style="width: 40px">Link</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>1.</td>
                      <td>Rechnung #13745</td>
                      <td>
                      <span class="badge badge-success">Bezahlt</span>
                      </td>
                      <td>link zu rechnung?id=13745</td>
                    </tr>
                    <tr>
                     <td>1.</td>
                      <td>Rechnung #13745</td>
                      <td>
                      <span class="badge badge-success">Bezahlt</span>
                      </td>
                      <td>link zu rechnung?id=13745</td>
                    </tr>
                    <tr>
                     <td>1.</td>
                      <td>Rechnung #13745</td>
                      <td>
                      <span class="badge badge-success">Bezahlt</span>
                      </td>
                      <td>link zu rechnung?id=13745</td>
                    </tr>
                    <tr>
                      <td>1.</td>
                      <td>Rechnung #13745</td>
                      <td>
                      <span class="badge badge-success">Bezahlt</span>
                      </td>
                      <td>link zu rechnung?id=13745</td>
                    </tr>
                  </tbody>
                </table>
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