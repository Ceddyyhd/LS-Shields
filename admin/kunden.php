<?php
include 'include/db.php';
ini_set('display_errors', 0);
error_reporting(0);

// Kunden-ID aus der URL holen (z.B. kunden.php?id=1)
$kunden_id = $_GET['id'] ?? null;
if (!$kunden_id) {
    die("Kunden-ID fehlt.");
}

// Kundeninformationen abrufen
$sql = "SELECT * FROM Kunden WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute(['id' => $kunden_id]);
$kunde = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kunde) {
    die("Kunde nicht gefunden.");
}

?>

<!DOCTYPE html>
<html lang="en">
<?php include 'include/header.php'; ?>

<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <?php include 'include/navbar.php'; ?>

  <!-- Main content -->
  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Kundenprofil</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Kundenprofil</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">
            <!-- Profil Bild -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle" src="path/to/default/image.jpg" alt="Kundenprofil Bild">
                </div>

                <h3 class="profile-username text-center"><?php echo htmlspecialchars($kunde['name']); ?></h3>
                <p class="text-muted text-center"><?php echo htmlspecialchars($kunde['umail']); ?></p>

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>Telefonnummer:</b> <a class="float-right"><?php echo htmlspecialchars($kunde['nummer']); ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>Erstellt am:</b> <a class="float-right"><?php echo htmlspecialchars($kunde['created_at']); ?></a>
                  </li>
                </ul>

                <button type="button" class="btn btn-primary" onclick="window.location.href='kunden_edit.php?id=<?php echo $kunde['id']; ?>'">Kunde Bearbeiten</button>
              </div>
            </div>
          </div>

          <!-- Kundendetails -->
          <div class="col-md-9">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#dokumente" data-toggle="tab">Dokumente</a></li>
                  <li class="nav-item"><a class="nav-link" href="#notizen" data-toggle="tab">Notizen</a></li>
                  <li class="nav-item"><a class="nav-link" href="#ausruestung" data-toggle="tab">Ausrüstung</a></li>
                </ul>
              </div>
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="dokumente">
                    

                  <div class="form-group row">
    <label for="uploadButton" class="col-sm-2 col-form-label">Dokumente Hochladen</label>
    <div class="col-sm-10">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-primary">
            Dokument hochladen
        </button>
    </div>
</div>

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
                <form id="uploadForm" action="include/upload_customer_document.php" method="POST" enctype="multipart/form-data">
                    <!-- Kunden-ID -->
                    <input type="hidden" name="customer_id" value="<?= htmlspecialchars($customer_id); ?>">

                    <div class="modal-body">
                        <!-- Eingabe für den benutzerdefinierten Namen -->
                        <div class="form-group">
                            <label for="documentName">Dokumentname</label>
                            <input type="text" id="documentName" name="document_name" class="form-control" placeholder="z.B. Vertrag" required>
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
    </div>
</div>
<script>
    document.getElementById("documentFile").addEventListener("change", function() {
        var fileName = this.files[0].name;
        var nextSibling = this.nextElementSibling;
        nextSibling.innerText = fileName; // Ändert den Text des Labels
    });

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
                    alert("Fehler beim Hochladen der Datei.");
                }
            });
        });
    });
</script>




                  </div>

                  <div class="tab-pane" id="notizen">
                    <!-- Notizen Anzeigen -->
                    <p>Notizen zum Kunden</p>
                  </div>

                  <div class="tab-pane" id="ausruestung">
                    <!-- Ausrüstung Anzeigen -->
                    <p>Ausrüstung des Kunden</p>
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
