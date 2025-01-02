<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<?php include 'include/header.php'; ?>

<body class="hold-transition sidebar-mini">
<div class="wrapper">

<?php include 'include/navbar.php'; ?>
<?php
include 'include/db.php'; // Datenbankverbindung
session_start(); // Sitzung starten

// SQL-Abfrage, um alle Vorschläge zu erhalten, einschließlich der Anzahl der Zustimmungen und Ablehnungen
$query = "SELECT id, vorschlag, status, erstellt_von, datum_uhrzeit, zustimmungen, ablehnungen FROM verbesserungsvorschlaege ORDER BY datum_uhrzeit DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$vorschlaege = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Starter Page</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Starter Page</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
   <?php
// Datenbankverbindung einbinden
include 'include/db.php';


?>


<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-vorschlag-create">
                Anfrage erstellen
            </button>      
          </div>




<!-- Verbesserungsvorschlag erstellen Modal -->
<div class="modal fade" id="modal-vorschlag-create">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Neuen Verbesserungsvorschlag erstellen</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="createSuggestionForm">
                   <div class="form-group">
                        <label for="bereich">Bereich (Personal, Ausruestung, Ausbildung, IT, Sonstiges)</label>
                        <input type="text" name="bereich" id="bereich" class="form-control" placeholder="Bereich eingeben" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="anonym">Anonym (Aktiviert = kein Name mitsenden)</label>
                        <input type="checkbox" name="anonym" id="anonym" class="form-check-input">
                    </div>

                    <div class="form-group">
                        <label for="betreff">Betreff</label>
                        <input type="text" name="betreff" id="betreff" class="form-control" placeholder="Betreff eingeben" required>
                    </div>

                    <div class="form-group">
                        <label for="vorschlag">Vorschlag</label>
                        <textarea name="vorschlag" id="vorschlag" class="form-control" rows="4" placeholder="Beschreiben Sie den Vorschlag" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
                <button type="button" class="btn btn-primary" id="saveRequestBtn">Speichern</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript zur Verarbeitung des Formulars -->
<script>
    document.getElementById('saveRequestBtn').addEventListener('click', function() {
        const formData = new FormData(document.getElementById('createSuggestionForm'));

        // Überprüfe, ob das Vorschlagsfeld ausgefüllt ist
        if (!formData.get('vorschlag')) {
            alert('Bitte den Vorschlag ausfüllen!');
            return;
        }

        // Zusätzliche Daten hinzufügen
        formData.append('status', 'Eingetroffen');
        formData.append('erstellt_von', '<?php echo $_SESSION["username"]; ?>');  // Hier den Ersteller aus der Session holen

        // AJAX-Anfrage senden
        fetch('include/vorschlag_create.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Vorschlag erfolgreich erstellt!');
                $('#modal-vorschlag-create').modal('hide'); // Schließt das Modal
                location.reload();  // Optional: Seite neu laden, um den neuen Vorschlag anzuzeigen
            } else {
                alert('Fehler: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Fehler:', error);
            alert('Ein unerwarteter Fehler ist aufgetreten.');
        });
    });
</script>

<div class="card-body">
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Ansprechpartner</th>
                <th>Anfrage</th>
                <th>Status</th>
                <th>Erstellt von</th>
                <th>Erstellt Am</th>
                <th>Details einblenden</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vorschlaege as $vorschlag): ?>
                <tr data-widget="expandable-table" data-id="<?= $vorschlag['id'] ?>" aria-expanded="false">
                    <td><?= htmlspecialchars($vorschlag['id']) ?></td>
                    <td><?= mb_strimwidth(htmlspecialchars($vorschlag['vorschlag']), 0, 50, '...') ?></td>
                    <td id="status-<?= $vorschlag['id'] ?>"><?= htmlspecialchars($vorschlag['status']) ?></td>
                    <td><?= htmlspecialchars($vorschlag['erstellt_von']) ?></td>
                    <td><?= htmlspecialchars($vorschlag['datum_uhrzeit']) ?></td>
                    <td>Details einblenden</td>
                </tr>
                <tr class="expandable-body" data-id="<?= $vorschlag['id'] ?>">
                    <td colspan="5">
                        <div class="p-3">
                            <div class="mb-3">
                                <strong>Vorschlag:</strong>
                                <div><?= htmlspecialchars($vorschlag['vorschlag']) ?></div>
                            </div>
                            <div class="mb-3">
                                <strong>Status:</strong>
                                <div><?= htmlspecialchars($vorschlag['status']) ?></div>
                            </div>
                            <div class="mb-3">
                                <strong>Erstellt von:</strong>
                                <div><?= htmlspecialchars($vorschlag['erstellt_von']) ?></div>
                            </div>
                            <div class="mb-3">
                                <strong>Datum & Uhrzeit:</strong>
                                <div><?= htmlspecialchars($vorschlag['datum_uhrzeit']) ?></div>
                            </div>

                            <div class="mb-3">
                                <strong>Zustimmungen:</strong> <?= $vorschlag['zustimmungen'] ?>
                            </div>
                            <div class="mb-3">
                                <strong>Ablehnungen:</strong> <?= $vorschlag['ablehnungen'] ?>
                            </div>

                            <div class="mb-3" id="buttons-<?= $vorschlag['id'] ?>">
                                <?php if ($vorschlag['status'] === 'Eingetroffen' && ($_SESSION['permissions']['change_to_in_bearbeitung_verbesserungen'] ?? false)): ?>
                                    <button class="btn btn-block btn-outline-warning" onclick="changeStatus(<?= $vorschlag['id'] ?>, 'change_status')">in Bearbeitung</button>
                                <?php elseif ($vorschlag['status'] === 'in Bearbeitung' && ($_SESSION['permissions']['change_to_in_planung_verbesserungen'] ?? false)): ?>
                                    <button class="btn btn-block btn-outline-info btn-lg" onclick="changeStatus(<?= $vorschlag['id'] ?>, 'move_to_eventplanung')">Abgeschlossen</button>
                                <?php elseif ($vorschlag['status'] === 'Abgeschlossen'): ?>
                                    <span class="badge badge-success">Abgeschlossen</span>
                                <?php endif; ?>
                            </div>

                            <!-- Buttons für Zustimmen / Ablehnen -->
                            <div class="mb-3">
                                <button class="btn btn-success" onclick="rateSuggestion(<?= $vorschlag['id'] ?>, true)">Zustimmen</button>
                                <button class="btn btn-danger" onclick="rateSuggestion(<?= $vorschlag['id'] ?>, false)">Ablehnen</button>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
      </div>
    </div>
  </div>
</div>

<script>
function rateSuggestion(vorschlagId, zustimmung) {
    fetch('include/rate_vorschlag.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${vorschlagId}&zustimmung=${zustimmung}`,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Überprüfe, ob das Element existiert, bevor du den Text änderst
            const zustimmungenElement = document.getElementById(`zustimmungen-${vorschlagId}`);
            if (zustimmungenElement) {
                zustimmungenElement.innerText = data.zustimmungen;
            }
            
            const ablehnungenElement = document.getElementById(`ablehnungen-${vorschlagId}`);
            if (ablehnungenElement) {
                ablehnungenElement.innerText = data.ablehnungen;
            }
        } else {
            alert('Fehler: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Fehler:', error);
        alert('Ein Fehler ist aufgetreten.');
    });
}
</script>


    
    
    

      


    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
    <div class="p-3">
      <h5>Title</h5>
      <p>Sidebar content</p>
    </div>
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
      Anything you want
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>
