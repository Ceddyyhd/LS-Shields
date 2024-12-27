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
  <?php if (isset($_SESSION['permissions']['user_create']) && $_SESSION['permissions']['user_create']): ?>
    <?php if (isset($_SESSION['permissions']['ausbildung_create']) && $_SESSION['permissions']['ausbildung_create']): ?>
  <div class="card-header">
    <h3 class="card-title">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-ausbildung-create">
            Ausbildungstyp erstellen
        </button>
    </h3>
  </div>
<?php endif; ?>

<!-- Modal für das Erstellen eines neuen Ausbildungstyps -->
<div class="modal fade" id="modal-ausbildung-create">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Ausbildungstyp Erstellen</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="createAusbildungForm">
                    <div class="form-group">
                        <label for="key_name">Key Name</label>
                        <input type="text" class="form-control" id="key_name" name="key_name" placeholder="Enter key name">
                    </div>
                    <div class="form-group">
                        <label for="display_name">Display Name</label>
                        <input type="text" class="form-control" id="display_name" name="display_name" placeholder="Enter display name">
                    </div>
                    <div class="form-group">
                        <label for="description">Beschreibung</label>
                        <textarea class="form-control" id="description" name="description" placeholder="Enter description"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveAusbildung">Speichern</button>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>
<div class="card-body">
  <table id="example1" class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>#</th>
        <th>Key Name</th>
        <th>Display Name</th>
        <th>Description</th>
        <th>Aktion</th>
      </tr>
    </thead>
    <tbody>
      <!-- Daten werden dynamisch geladen -->
    </tbody>
    <tfoot>
      <tr>
        <th>#</th>
        <th>Key Name</th>
        <th>Display Name</th>
        <th>Description</th>
        <th>Aktion</th>
      </tr>
    </tfoot>
  </table>
    </div>
  </div>
</div>




<!-- Dein JavaScript -->
<script>
  $(document).ready(function () {
    // Ajax-Anfrage zum Laden der Ausbildungstypen
    $.ajax({
      url: 'include/fetch_ausbildungstypen.php',
      type: 'POST',
      dataType: 'json',
      success: function (data) {
          console.log(data);

          if (!Array.isArray(data)) {
              console.error('Die Antwort ist kein Array:', data);
              alert('Fehler: Antwort ist kein Array.');
              return;
          }

          let tableBody = $('#example1 tbody');
          tableBody.empty();

          data.forEach(item => {
              tableBody.append(`
                  <tr>
                      <td>${item.id}</td>
                      <td>${item.key_name}</td>
                      <td>${item.display_name}</td>
                      <td>${item.description}</td>
                      <td>
                          <a href="/admin/edit_ausbildungstyp.php?id=${item.id}" class="btn btn-outline-secondary">Bearbeiten</a>
                          <button class="btn btn-outline-danger" onclick="deleteAusbildungTyp(${item.id})">Löschen</button>
                      </td>
                  </tr>
              `);
          });
      },
      error: function (xhr, status, error) {
          console.error('AJAX-Fehler:', xhr.responseText);
          alert('Fehler beim Abrufen der Daten.');
      }
    });

    // Listener für den "Speichern"-Button im Modal
    $('#saveAusbildung').click(function() {
        const formData = new FormData(document.getElementById('createAusbildungForm'));
        
        $.ajax({
            url: 'include/create_ausbildungstyp.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                alert('Ausbildungstyp erfolgreich erstellt.');
                location.reload();
            },
            error: function(xhr, status, error) {
                console.error('Fehler beim Erstellen:', xhr.responseText);
                alert('Fehler beim Erstellen des Ausbildungstyps.');
            }
        });
    });
  });

  // Löschen-Funktion
  function deleteAusbildungTyp(id) {
    if (confirm('Möchten Sie diesen Ausbildungstyp wirklich löschen?')) {
        $.ajax({
            url: 'include/delete_ausbildungstyp.php',
            type: 'POST',
            data: { id: id },
            success: function(response) {
                alert('Ausbildungstyp gelöscht');
                location.reload();
            },
            error: function(xhr, status, error) {
                console.error('Fehler beim Löschen:', xhr.responseText);
                alert('Fehler beim Löschen des Ausbildungstyps.');
            }
        });
    }
  }
</script>

<!-- JavaScript Section -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.6/js/dataTables.bootstrap4.min.js"></script>


  <!-- /.col -->
</div>
<!-- /.row -->
</div>
<!-- /.container-fluid -->
</section>
    
    
    

      


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
