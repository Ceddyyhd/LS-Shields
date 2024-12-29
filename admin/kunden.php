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
$query = "SELECT id, unternehmen_name, ansprechperson_name, ansprechperson_nummer, adresse, unternehmen_art FROM kunden";
$stmt = $conn->prepare($query);
$stmt->execute();
$anfragen = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-kunde-create">
                Kunden erstellen
            </button>      </div>




<!-- Anfrage erstellen Modal -->
<div class="modal fade" id="modal-kunde-create">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Kunde erstellen</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="createCustomerForm">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="unternehmen_name">Unternehmen Name</label>
                            <input type="text" class="form-control" id="unternehmen_name" name="unternehmen_name" placeholder="Enter Unternehmen Name" required>
                        </div>
                        <div class="form-group">
                            <label for="ansprechperson_name">Ansprechperson Name</label>
                            <input type="text" class="form-control" id="ansprechperson_name" name="ansprechperson_name" placeholder="Enter Ansprechperson Name" required>
                        </div>
                        <div class="form-group">
                            <label for="ansprechperson_nummer">Tel. Nr.</label>
                            <input type="text" class="form-control" id="ansprechperson_nummer" name="ansprechperson_nummer" placeholder="Enter Telefonnummer" required>
                        </div>
                        <div class="form-group">
                            <label for="adresse">Adresse</label>
                            <textarea name="adresse" id="adresse" class="form-control" rows="4" placeholder="Enter Adresse" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="unternehmen_art">Unternehmen Art</label>
                            <input type="text" class="form-control" id="unternehmen_art" name="unternehmen_art" placeholder="Enter Unternehmen Art" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
                <button type="button" class="btn btn-primary" id="saveCustomerBtn">Speichern</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript zur Verarbeitung des Formulars -->
<script>
    document.getElementById('saveCustomerBtn').addEventListener('click', function() {
    const formData = new FormData(document.getElementById('createCustomerForm'));

    // Überprüfen, ob alle Felder ausgefüllt sind
    if (!formData.get('unternehmen_name') || !formData.get('ansprechperson_name') || !formData.get('ansprechperson_nummer') || !formData.get('adresse') || !formData.get('unternehmen_art')) {
        alert('Bitte alle Felder ausfüllen!');
        return;
    }

    // AJAX-Anfrage senden
    fetch('include/kunden_create.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Kunde erfolgreich erstellt!');
            $('#modal-kunde-create').modal('hide'); // Schließt das Modal
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
              <th>Unternehmen</th>
              <th>Ansprechperson</th>
              <th>Ansprechperson Nummer</th>
              <th>Details einblenden</th>
            </tr>
          </thead>
          <tbody>
<?php foreach ($anfragen as $anfrage): ?>
  <tr data-widget="expandable-table" data-id="<?= $anfrage['id'] ?>" aria-expanded="false">
    <td><?= htmlspecialchars($anfrage['id']) ?></td>
    <td><?= htmlspecialchars($anfrage['unternehmen_name']) ?></td>
    <td>
      <?= mb_strimwidth(htmlspecialchars($anfrage['ansprechperson_name']), 0, 50, '...') ?>
    </td>
    <td id="status-<?= $anfrage['id'] ?>"><?= htmlspecialchars($anfrage['ansprechperson_nummer']) ?></td>
    <td>Details einblenden</td>
  </tr>
  <tr class="expandable-body" data-id="<?= $anfrage['id'] ?>">
    <td colspan="5">
      <div class="p-3">
        <div class="mb-3">
          <strong>Unternehmen:</strong>
          <div><?= htmlspecialchars($anfrage['unternehmen_name']) ?></div>
        </div>
        <div class="mb-3">
          <strong>Ansprechperson & Nummer:</strong>
          <div><?= htmlspecialchars($anfrage['ansprechperson_name']) ?></div>
          <div><?= htmlspecialchars($anfrage['ansprechperson_nummer']) ?></div>
        </div>
        <div class="mb-3">
          <strong>Adresse:</strong>
          <div><?= htmlspecialchars($anfrage['adresse']) ?></div>
        </div>
        <div class="mb-3">
          <strong>Unternehmen Art:</strong>
          <div><?= htmlspecialchars($anfrage['unternehmen_art']) ?></div>
        </div>
        <div class="mb-3">
          <div><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#rechnung-erstellen">
                Rechnung erstellen
            </button></div>
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



<div class="modal fade" id="rechnung-erstellen">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Default Modal</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
            <div class="form-group">
              <label>Text Disabled</label>
              <input type="text" class="form-control" placeholder="Unternehmen" disabled>
            </div>
            <div class="form-group">
              <label>Text Disabled</label>
              <input type="text" class="form-control" placeholder="Ansprechperson" disabled>
            </div>
            <div class="form-group">
              <label>Text Disabled</label>
              <input type="text" class="form-control" placeholder="Nummer" disabled>
            </div>
            <hr>
            <div class="row">
            <div class="col-5">
            <p>Beschreibung</p>
              <input type="text" class="form-control" placeholder="">
            </div>
            <div class="col-3">
              <p>Stück Preis</p>
              <input type="text" class="form-control" placeholder="">
            </div>
            <div class="col-3">
              <p>Anzahl</p>
              <input type="text" class="form-control" placeholder="">
            </div>
                </div>
            <hr>
            <div class="col-3" style="margin-left: 315px;">
              <p>Rabatt in %</p>
              <input type="text" class="form-control" placeholder="">
          </div>

            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary">Save changes</button>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>

    
    
    

      


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
