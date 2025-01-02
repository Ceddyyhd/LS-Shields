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

// Anfragen aus der Datenbank abrufen
$query = "SELECT id, vorname_nachname, telefonnummer, anfrage, datum_uhrzeit, erstellt_von, status FROM anfragen ORDER BY datum_uhrzeit DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$anfragen = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-anfrage-create">
                Anfrage erstellen
            </button>      </div>




<!-- Anfrage erstellen Modal -->
<div class="modal fade" id="modal-anfrage-create">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?php echo htmlspecialchars($user_name); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="createRequestForm">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter name" required>
                        </div>
                        <div class="form-group">
                            <label for="nummer">Tel. Nr.</label>
                            <input type="text" class="form-control" id="nummer" name="nummer" placeholder="Enter nummer" required>
                        </div>
                        <div class="form-group">
                            <label for="anfrage">Anfrage</label>
                            <textarea name="anfrage" id="anfrage" class="form-control" rows="4" placeholder="Bitte teilen Sie uns Ihre Anfrage mit." required></textarea>
                        </div>
                      <!-- Hidden Field für 'erstellt_von' -->
                     <input type="hidden" id="erstellt_von" name="erstellt_von" value="<?php echo $_SESSION['username']; ?>">

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
    const formData = new FormData(document.getElementById('createRequestForm'));

    // Überprüfe, ob alle Felder ausgefüllt sind
    if (!formData.get('name') || !formData.get('nummer') || !formData.get('anfrage')) {
        alert('Bitte alle Felder ausfüllen!');
        return;
    }

    // Füge zusätzliche Daten hinzu (falls erforderlich, z.B. für Status)
    formData.append('status', 'Eingetroffen');
    
    // AJAX-Anfrage senden
    fetch('include/anfrage_create.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Anfrage erfolgreich erstellt!');
            $('#modal-anfrage-create').modal('hide'); // Schließt das Modal
            location.reload();  // Optional: Seite neu laden, um die neue Anfrage zu sehen
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
                <th>Details einblenden</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($anfragen as $anfrage): ?>
            <tr data-widget="expandable-table" data-id="<?= $anfrage['id'] ?>" aria-expanded="false">
                <td><?= htmlspecialchars($anfrage['id']) ?></td>
                <td><?= htmlspecialchars($anfrage['vorname_nachname']) ?></td>
                <td>
                    <?= mb_strimwidth(htmlspecialchars($anfrage['anfrage']), 0, 50, '...') ?>
                </td>
                <td id="status-<?= $anfrage['id'] ?>"><?= htmlspecialchars($anfrage['status']) ?></td>
                <td><?= htmlspecialchars($anfrage['erstellt_von']) ?></td>
                <td>Details einblenden</td>
            </tr>
            <tr class="expandable-body" data-id="<?= $anfrage['id'] ?>">
    <td colspan="5">
        <div class="p-3">
            <div class="mb-3">
                <strong>Datum & Uhrzeit:</strong>
                <div><?= htmlspecialchars($anfrage['datum_uhrzeit']) ?></div>
            </div>
            <div class="mb-3">
                <strong>Telefonnummer:</strong>
                <div><?= htmlspecialchars($anfrage['telefonnummer']) ?></div>
            </div>
            <div class="mb-3">
                <strong>Status:</strong>
                <div><?= htmlspecialchars($anfrage['status']) ?></div>
            </div>
            <div class="mb-3">
                <strong>Anfrage:</strong>
                <div><?= htmlspecialchars($anfrage['anfrage']) ?></div>
            </div>
            <div class="mb-3" id="buttons-<?= $anfrage['id'] ?>">
                <!-- Form für Statusänderung -->
                <form id="status-form-<?= $anfrage['id'] ?>">
                    <input type="hidden" name="id" value="<?= $anfrage['id'] ?>"> <!-- ID aus der Anfrage -->
                    <input type="hidden" name="erstellt_von" value="<?= $_SESSION['username'] ?>"> <!-- Benutzername aus der Session -->
                    <?php if ($anfrage['status'] === 'Eingetroffen' && ($_SESSION['permissions']['change_to_in_bearbeitung'] ?? false)): ?>
                        <button class="btn btn-block btn-outline-warning" type="button" onclick="changeStatus(<?= $anfrage['id'] ?>, 'change_status')">in Bearbeitung</button>
                    <?php elseif ($anfrage['status'] === 'in Bearbeitung' && ($_SESSION['permissions']['change_to_in_planung'] ?? false)): ?>
                        <button class="btn btn-block btn-outline-info btn-lg" type="button" onclick="changeStatus(<?= $anfrage['id'] ?>, 'move_to_eventplanung')">in Planung</button>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Logs anzeigen -->
            <div class="mt-3">
                <strong>Log-Einträge:</strong>
                <ul>
                    <?php
                    // Logs für diese Anfrage aus der anfragen_logs-Tabelle abfragen
                    $stmt = $conn->prepare("SELECT * FROM anfragen_logs WHERE anfrage_id = :id ORDER BY timestamp DESC");
                    $stmt->execute([':id' => $anfrage['id']]);
                    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Logs ausgeben
                    if ($logs) {
                        foreach ($logs as $log) {
                            echo '<li>'.htmlspecialchars($log['username']).'-' . htmlspecialchars($log['action']) . ' - ' . htmlspecialchars($log['timestamp']) . '</li>';
                        }
                    } else {
                        echo '<li>Keine Log-Einträge gefunden.</li>';
                    }
                    ?>
                </ul>
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
function changeStatus(id, action) {
  const form = document.getElementById(`status-form-${id}`);
  const formData = new FormData(form);

  // Füge die action manuell hinzu
  formData.append('action', action);

  // Überprüfe, ob die ID korrekt übermittelt wird
  console.log("FormData:", formData);

  fetch('include/update_status.php', {
    method: 'POST',
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        // Weiterer Code zur Verarbeitung
        if (action === 'change_status') {
          document.getElementById(`status-${id}`).innerText = 'in Bearbeitung';
          document.getElementById(`buttons-${id}`).innerHTML =
            `<button class="btn btn-block btn-outline-info btn-lg" onclick="changeStatus(${id}, 'move_to_eventplanung')">in Planung</button>`;
        } else if (action === 'move_to_eventplanung' && data.removed) {
          document.querySelector(`tr[data-widget="expandable-table"][data-id="${id}"]`).remove();
          document.querySelector(`tr.expandable-body[data-id="${id}"]`).remove();
        }
      } else {
        alert('Fehler: ' + (data.error || 'Unbekannter Fehler'));
      }
    })
    .catch((error) => {
      alert('Ein Fehler ist aufgetreten: ' + error.message);
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
