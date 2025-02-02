<?php
include 'include/db.php';  // Datenbankverbindung einbinden

// Mitarbeiter (users) Dropdown
$query_users = "SELECT id, name FROM users";
$stmt_users = $conn->prepare($query_users);
$stmt_users->execute();
$users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

// SQL-Abfrage für Urlaubsanträge mit Status 'pending'
$query_pending_vacations = "
    SELECT v.*, u.name as employee_name
    FROM vacations v
    JOIN users u ON v.user_id = u.id
    WHERE v.status = 'pending' AND v.end_date >= CURDATE()
";
$stmt_pending = $conn->prepare($query_pending_vacations);
$stmt_pending->execute();
$pending_vacations = $stmt_pending->fetchAll(PDO::FETCH_ASSOC);

// SQL-Abfrage für Urlaubsanträge mit Status 'approved'
$query_approved_vacations = "
    SELECT v.*, u.name as employee_name
    FROM vacations v
    JOIN users u ON v.user_id = u.id
    WHERE v.status = 'approved' AND v.end_date >= CURDATE()
";
$stmt_approved = $conn->prepare($query_approved_vacations);
$stmt_approved->execute();
$approved_vacations = $stmt_approved->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!DOCTYPE html>
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
            <h1 class="m-0">Urlaubsverwaltung</h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- Urlaub Formular für Admin -->
          <div class="col-md-3">
    <div class="sticky-top mb-3">
    <?php if ($_SESSION['permissions']['add_urlaub_for_users'] ?? false): ?>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Urlaub einreichen</h3>
            </div>
            <div class="card-body">
                <form id="vacationForm">
                    <div class="form-group">
                        <label>Mitarbeiter</label>
                        <select class="form-control" name="user_id" required>
                            <option value="">Wählen Sie einen Mitarbeiter</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['id']; ?>"><?php echo $user['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Start Datum</label>
                        <input type="date" class="form-control" name="start_date" required>
                    </div>
                    <div class="form-group">
                        <label>End Datum</label>
                        <input type="date" class="form-control" name="end_date" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Notiz</label>
                        <input type="text" class="form-control" name="note">
                    </div>
                    <!-- Hidden Input für den Benutzernamen -->
                    <input type="hidden" name="user_name" value="<?php echo $_SESSION['username']; ?>">

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Urlaub Erstellen</button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>


          <!-- Urlaubsanträge mit Status 'pending' -->
          <div class="col-md-9">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Urlaubsanträge (Pending)</h3>
              </div>
              <div class="card-body">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Mitarbeiter</th>
                      <th>Start Datum</th>
                      <th>End Datum</th>
                      <th>Status</th>
                      <th>Aktion</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($pending_vacations as $vacation): ?>
                        <tr>
                          <td><?php echo $vacation['id']; ?></td>
                          <td><?php echo $vacation['employee_name']; ?></td>
                          <td><?php echo $vacation['start_date']; ?></td>
                          <td><?php echo $vacation['end_date']; ?></td>
                          <td><span class="badge bg-warning"><?php echo ucfirst($vacation['status']); ?></span></td>
                          <td>
                          <?php if ($_SESSION['permissions']['edit_urlaub_users'] ?? false): ?>
                          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#vacation-bearbeiten" data-id="<?php echo $vacation['id']; ?>">Bearbeiten</button>
                          <?php endif; ?>
                        </td>
                        </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Urlaubsanträge mit Status 'approved' -->
            <div class="card">
    <div class="card-header">
        <h3 class="card-title">Genehmigte Urlaubsanträge</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Mitarbeiter</th>
                    <th>Start Datum</th>
                    <th>End Datum</th>
                    <th>Status</th>
                    <th>Aktion</th> <!-- Neue Spalte für den Bearbeiten-Button -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($approved_vacations as $vacation): ?>
                    <tr>
                        <td><?php echo $vacation['id']; ?></td>
                        <td><?php echo $vacation['employee_name']; ?></td>
                        <td><?php echo $vacation['start_date']; ?></td>
                        <td><?php echo $vacation['end_date']; ?></td>
                        <td><span class="badge bg-success"><?php echo ucfirst($vacation['status']); ?></span></td>
                        <!-- Bearbeiten-Button hinzufügen -->
                        <td>
                        <?php if ($_SESSION['permissions']['edit_urlaub_users'] ?? false): ?>
                            <button type="button" class="btn btn-primary edit-button" data-toggle="modal" data-target="#vacation-bearbeiten" data-id="<?php echo $vacation['id']; ?>">
                                Bearbeiten
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
  </div><!-- /.content-wrapper -->

  <!-- Modal zum Bearbeiten eines Urlaubs -->
  <div class="modal fade" id="vacation-bearbeiten">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Urlaub Bearbeiten</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="vacationEditForm">
                    <div class="form-group">
                        <label>Start Datum</label>
                        <input type="date" class="form-control" id="edit-start_date" name="start_date" required>
                    </div>
                    <div class="form-group">
                        <label>End Datum</label>
                        <input type="date" class="form-control" id="edit-end_date" name="end_date" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" id="edit-status" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Notiz</label>
                        <input type="text" class="form-control" id="edit-note" name="note" placeholder="Notiz">
                    </div>
                    <input type="hidden" id="edit-vacation_id" name="vacation_id">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="saveVacationChanges">Speichern</button>
                    </div>
                    <input type="hidden" name="user_name" value="<?php echo $_SESSION['username']; ?>">
                </form>
            </div>
        </div>
    </div>
</div>

  </div>
  <!-- /.content-wrapper -->
  <script>
$(document).ready(function() {
    // Event-Listener für den Klick auf den Bearbeiten-Button
    $('[data-toggle="modal"][data-target="#vacation-bearbeiten"]').on('click', function() {
        var vacationId = $(this).data('id'); // Hole die Urlaubs-ID aus dem data-id-Attribut

        $.ajax({
            url: 'include/vacation_fetch.php', // PHP-Skript, um die Urlaubsdaten zu holen
            method: 'GET',
            data: { id: vacationId },  // Sende die Urlaubs-ID an das Skript
            success: function(response) {
                var vacation = JSON.parse(response);  // Die Antwort wird als JSON geparsed

                if (vacation.success) {
                    // Fülle die Modal-Felder mit den abgerufenen Daten
                    $('#edit-vacation_id').val(vacation.id);
                    $('#edit-start_date').val(vacation.start_date);
                    $('#edit-end_date').val(vacation.end_date);
                    $('#edit-status').val(vacation.status);
                    $('#edit-note').val(vacation.note); // Füge die Notiz hinzu
                } else {
                    alert('Fehler beim Laden der Urlaubsantragsdaten: ' + vacation.message);
                }
            },
            error: function() {
                alert('Fehler bei der Anfrage!');
            }
        });
    });

    $('#vacationForm').on('submit', function(e) {
        e.preventDefault();  // Verhindert das Standardverhalten des Formulars

        var formData = $(this).serialize();  // Alle Formulardaten sammeln (einschließlich des 'user_name' versteckten Feldes)

        $.ajax({
            url: 'include/vacation_create_leitung.php',  // Die PHP-Datei für die Erstellung des Urlaubsantrags
            method: 'POST',
            data: formData,  // Formulardaten, einschließlich 'user_name'
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    alert('Urlaubsantrag erfolgreich erstellt!');
                    location.reload();  // Seite neu laden, um den neuen Antrag anzuzeigen
                } else {
                    alert('Fehler beim Erstellen des Urlaubsantrags: ' + data.message);
                }
            },
            error: function() {
                alert('Fehler bei der Anfrage!');
            }
        });
    });


    // Änderungen speichern
    $('#saveVacationChanges').on('click', function() {
        var formData = $('#vacationEditForm').serialize(); // Alle Formulardaten sammeln

        $.ajax({
            url: 'include/vacation_update.php', // PHP-Skript zum Bearbeiten des Urlaubsantrags
            method: 'POST',
            data: formData,
            success: function(response) {
                var data = JSON.parse(response); // Antwort als JSON parsen
                if (data.success) {
                    alert('Urlaubsantrag erfolgreich bearbeitet!');
                    location.reload(); // Seite neu laden, um die Änderungen anzuzeigen
                } else {
                    alert('Fehler beim Bearbeiten des Urlaubsantrags: ' + data.message);
                }
            },
            error: function() {
                alert('Fehler bei der Anfrage!');
            }
        });
    });
});
</script>

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
