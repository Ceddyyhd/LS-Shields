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

// Abruf aller Vorschläge aus der Datenbank
$query = "SELECT * FROM verbesserungsvorschlaege ORDER BY datum_uhrzeit DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$vorschlaege = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Sicherstellen, dass eine ID übergeben wurde
$vorschlag = null; // Setze $vorschlag standardmäßig auf null, falls keine ID übergeben wird
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $vorschlagId = $_GET['id'];

    // SQL-Abfrage, um nur den spezifischen Vorschlag zu holen
    $query = "SELECT * FROM verbesserungsvorschlaege WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $vorschlagId, PDO::PARAM_INT); // Bindet die ID sicher
    $stmt->execute();
    $vorschlag = $stmt->fetch(PDO::FETCH_ASSOC);

    // Überprüfen, ob der Vorschlag existiert
    if (!$vorschlag) {
        // Fehlerbehandlung, falls der Vorschlag nicht gefunden wurde
        echo "Vorschlag nicht gefunden!";
        // Optionale Fehlermeldung, aber wir beenden das Skript nicht
    }
} else {
    echo "Keine Vorschlags-ID angegeben!";
    // Optionale Fehlermeldung, aber wir beenden das Skript nicht
}
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
                  <label>Bereich</label>
                  <select class="custom-select" name="bereich">
                      <option value="Personal">Personal</option>
                      <option value="Ausrüstung">Ausrüstung</option>
                      <option value="Ausbildung">Ausbildung</option>
                      <option value="IT">IT</option>
                      <option value="Sonstiges">Sonstiges</option>
                  </select>
              </div>

              <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" id="anonym" class="form-check-input" name="fuel_checked" value="true">
                    <label for="anonym">Anonym (Aktiviert = kein Name mitsenden)</label>
                </div>
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


<!-- Verbesserungsvorschlag bearbeiten Modal -->
<?php if ($vorschlag): ?>
<div class="modal fade" id="modal-vorschlag-bearbeiten">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Vorschlag bearbeiten</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editSuggestionForm">
                    <!-- Bereich -->
                    <div class="form-group">
                        <label for="bereich">Bereich</label>
                        <select class="custom-select" name="bereich" id="bereich">
                            <option value="Personal" <?php echo ($vorschlag['bereich'] == 'Personal') ? 'selected' : ''; ?>>Personal</option>
                            <option value="Ausrüstung" <?php echo ($vorschlag['bereich'] == 'Ausrüstung') ? 'selected' : ''; ?>>Ausrüstung</option>
                            <option value="Ausbildung" <?php echo ($vorschlag['bereich'] == 'Ausbildung') ? 'selected' : ''; ?>>Ausbildung</option>
                            <option value="IT" <?php echo ($vorschlag['bereich'] == 'IT') ? 'selected' : ''; ?>>IT</option>
                            <option value="Sonstiges" <?php echo ($vorschlag['bereich'] == 'Sonstiges') ? 'selected' : ''; ?>>Sonstiges</option>
                        </select>
                    </div>

                    <!-- Anonym Checkbox -->
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" id="anonym" class="form-check-input" name="fuel_checked" <?php echo ($vorschlag['anonym'] == '1') ? 'checked' : ''; ?>>
                            <label for="anonym">Anonym (Aktiviert = kein Name mitsenden)</label>
                        </div>
                    </div>

                    <!-- Betreff -->
                    <div class="form-group">
                        <label for="betreff">Betreff</label>
                        <input type="text" name="betreff" id="betreff" class="form-control" placeholder="Betreff eingeben" value="<?php echo htmlspecialchars($vorschlag['betreff']); ?>">
                    </div>

                    <!-- Vorschlag -->
                    <div class="form-group">
                        <label for="vorschlag">Vorschlag</label>
                        <textarea name="vorschlag" id="vorschlag" class="form-control" rows="4" placeholder="Vorschlag beschreiben"><?php echo htmlspecialchars($vorschlag['vorschlag']); ?></textarea>
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="custom-select" name="status" id="status">
                            <option value="Angefragt" <?php echo ($vorschlag['status'] == 'Angefragt') ? 'selected' : ''; ?>>Angefragt</option>
                            <option value="in Bearbeitung" <?php echo ($vorschlag['status'] == 'in Bearbeitung') ? 'selected' : ''; ?>>in Bearbeitung</option>
                            <option value="Rückfragen" <?php echo ($vorschlag['status'] == 'Rückfragen') ? 'selected' : ''; ?>>Rückfragen</option>
                            <option value="Angenommen" <?php echo ($vorschlag['status'] == 'Angenommen') ? 'selected' : ''; ?>>Angenommen</option>
                            <option value="Abgelehnt" <?php echo ($vorschlag['status'] == 'Abgelehnt') ? 'selected' : ''; ?>>Abgelehnt</option>
                        </select>
                    </div>

                    <!-- Notiz -->
                    <div class="form-group">
                        <label for="notiz">Notiz</label>
                        <textarea name="notiz" id="notiz" class="form-control" rows="4" placeholder="Notizen hinzufügen"><?php echo htmlspecialchars($vorschlag['notiz']); ?></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
                <button type="button" class="btn btn-primary" id="saveEditBtn">Speichern</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>



<script>
 function openEditModal() {
    // Hole die gespeicherten Daten aus dem versteckten Bereich
    const hiddenData = document.getElementById('hidden-vorschlag-data');
    
    const vorschlagId = hiddenData.getAttribute('data-id');
    const bereich = hiddenData.getAttribute('data-bereich');
    const anonym = hiddenData.getAttribute('data-anonym') === "1"; // Falls "1", dann angekreuzt
    const betreff = hiddenData.getAttribute('data-betreff');
    const vorschlagText = hiddenData.getAttribute('data-vorschlag');
    const status = hiddenData.getAttribute('data-status');
    const notiz = hiddenData.getAttribute('data-notiz');

    // Fülle die Felder im Modal
    document.getElementById('bereich').value = bereich;
    document.getElementById('anonym').checked = anonym;
    document.getElementById('betreff').value = betreff;
    document.getElementById('vorschlag').value = vorschlagText;
    document.getElementById('status').value = status;
    document.getElementById('notiz').value = notiz;

    // Öffne das Modal
    $('#modal-vorschlag-bearbeiten').modal('show');
}








  document.getElementById('saveEditBtn').addEventListener('click', function() {
    const formData = new FormData(document.getElementById('editSuggestionForm'));

    // Überprüfe, ob alle Felder ausgefüllt sind
    if (!formData.get('status') || !formData.get('notiz') || !formData.get('betreff') || !formData.get('vorschlag')) {
        alert('Bitte alle Felder ausfüllen!');
        return;
    }

    // Sende die AJAX-Anfrage zum Speichern der Änderungen
    fetch('include/update_vorschlag.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Vorschlag erfolgreich bearbeitet!');
            $('#modal-vorschlag-bearbeiten').modal('hide'); // Modal schließen
            location.reload(); // Optional: Seite neu laden, um die Änderungen zu sehen
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


<!-- JavaScript zur Verarbeitung des Formulars -->
<script>
    document.getElementById('saveRequestBtn').addEventListener('click', function() {
    const formData = new FormData(document.getElementById('createSuggestionForm'));

    // Überprüfe, ob das Vorschlagsfeld ausgefüllt ist
    console.log("FormData:", formData);

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
        console.log('Antwort vom Server:', data);
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
                <th>Bereich</th>
                <th>Vorschlag</th>
                <th>Betreff</th>
                <th>Datum & Uhrzeit</th>
                <th>Status</th>
                <th>Details einblenden</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vorschlaege as $vorschlag): ?>
                <tr data-widget="expandable-table" data-id="<?= $vorschlag['id'] ?>" aria-expanded="false">
                    <td><?= htmlspecialchars($vorschlag['id']) ?></td>
                    <td><?= mb_strimwidth(htmlspecialchars($vorschlag['vorschlag']), 0, 50, '...') ?></td>
                    <td><?= mb_strimwidth(htmlspecialchars($vorschlag['betreff']), 0, 25, '...') ?></td>
                    <td><?= htmlspecialchars($vorschlag['datum_uhrzeit']) ?></td>
                    <td><?= htmlspecialchars($vorschlag['status']) ?></td>
                    <td><?= htmlspecialchars($vorschlag['erstellt_von']) ?></td>
                    <td>Details einblenden</td>
                </tr>
                <tr class="expandable-body" data-id="<?= $vorschlag['id'] ?>">
                    <td colspan="6">
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
                              <strong>Zustimmungen:</strong> <span id="zustimmungen-<?= $vorschlag['id'] ?>"><?= $vorschlag['zustimmungen'] ?></span>
                          </div>
                          <div class="mb-3">
                              <strong>Ablehnungen:</strong> <span id="ablehnungen-<?= $vorschlag['id'] ?>"><?= $vorschlag['ablehnungen'] ?></span>
                          </div>

                            <!-- Buttons für Zustimmen / Ablehnen -->
                            <div class="mb-3">
                            <button class="btn btn-success" id="btn-accept-<?= $vorschlag['id'] ?>" onclick="rateSuggestion(<?= $vorschlag['id'] ?>, true)">
                              Zustimmen 
                          </button>
                          <button class="btn btn-danger" id="btn-reject-<?= $vorschlag['id'] ?>" onclick="rateSuggestion(<?= $vorschlag['id'] ?>, false)">
                              Ablehnen 
                          </button>      
                          <<!-- Button zum Öffnen des Modals -->
                            <button class="btn btn-info btn-sm" 
                                    data-toggle="modal" data-target="#modal-vorschlag-bearbeiten" 
                                    data-id="<?= $vorschlag['id'] ?>">
                                Anfrage bearbeiten
                            </button>                
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
            // Dynamisch die Anzeige der Zustimmungen und Ablehnungen aktualisieren
            document.getElementById(`zustimmungen-${vorschlagId}`).innerText = data.zustimmungen;
            document.getElementById(`ablehnungen-${vorschlagId}`).innerText = data.ablehnungen;
            
            // Deaktiviere die Buttons, wenn der Benutzer abgestimmt hat
            document.getElementById(`btn-accept-${vorschlagId}`).disabled = true;
            document.getElementById(`btn-reject-${vorschlagId}`).disabled = true;
        } else {
            alert('Fehler: ' + data.message); // Zeige Fehlermeldung an
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
