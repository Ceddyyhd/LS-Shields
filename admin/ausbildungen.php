<?php
session_start(); // Ensure session is started

// CSRF-Token generieren und in der Session speichern
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>

<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<?php include 'include/header.php'; ?>

<body class="hold-transition sidebar-mini dark-mode">
<div class="wrapper">

<?php include 'include/navbar.php'; ?>
<!-- jQuery (notwendig für Bootstrap) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

  <!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Starter Page</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Starter Page</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <div class="card">
  <?php if (isset($_SESSION['permissions']['ausbildung_create']) && $_SESSION['permissions']['ausbildung_create']): ?>
  <div class="card-header">
    <h3 class="card-title">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-ausbildung-create">
            Ausbildungstyp erstellen
        </button>
    </h3>
  </div>
  <?php endif; ?>

  <!-- Ausbildungstyp erstellen Modal -->
  <div class="modal fade" id="modal-ausbildung-create">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Ausbildungstyp erstellen</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="createAusbildungForm">
            <div class="card-body">
              <div class="form-group">
                <label for="title">Titel</label>
                <input type="text" class="form-control" id="title" name="title" placeholder="Titel eingeben" required>
              </div>
              <div class="form-group">
                <label for="description">Beschreibung</label>
                <textarea name="description" id="description" class="form-control" rows="4" placeholder="Beschreibung eingeben" required></textarea>
              </div>
              <!-- Hidden Field für CSRF-Token -->
              <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            </div>
          </form>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
          <button type="button" class="btn btn-primary" id="saveAusbildungBtn">Speichern</button>
        </div>
      </div>
    </div>
  </div>
  <!-- /.modal -->
</div>
<!-- /.card -->

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

<!-- JavaScript zur Verarbeitung des Formulars -->
<script>
document.getElementById('saveAusbildungBtn').addEventListener('click', function() {
    const formData = new FormData(document.getElementById('createAusbildungForm'));

    fetch('include/ausbildung_create.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Ausbildungstyp erfolgreich erstellt');
            // Optionally, close the modal and reset the form
            $('#modal-ausbildung-create').modal('hide');
            document.getElementById('createAusbildungForm').reset();
        } else {
            alert('Fehler: ' + (data.message || 'Unbekannter Fehler'));
        }
    })
    .catch(error => {
        alert('Ein Fehler ist aufgetreten: ' + error.message);
    });
});

// Fetch Ausbildungstypen with CSRF token
function fetchAusbildungstypen() {
    const formData = new FormData();
    formData.append('csrf_token', '<?php echo $csrf_token; ?>');

    fetch('include/fetch_ausbildungstypen.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Handle the fetched data
            console.log(data.ausbildungstypen);
        } else {
            alert('Fehler: ' + (data.message || 'Unbekannter Fehler'));
        }
    })
    .catch(error => {
        alert('Ein Fehler ist aufgetreten: ' + error.message);
    });
}

// Call the function to fetch Ausbildungstypen
fetchAusbildungstypen();
</script>

</body>
</html>
