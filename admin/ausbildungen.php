<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<?php include 'include/header.php'; ?>

<body class="hold-transition sidebar-mini dark-mode">
<div class="wrapper">
<head>
    <!-- summernote -->
    <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- daterange picker -->
    <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Bootstrap Color Picker -->
    <link rel="stylesheet" href="plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <!-- Bootstrap4 Duallistbox -->
    <link rel="stylesheet" href="plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">
    <!-- BS Stepper -->
    <link rel="stylesheet" href="plugins/bs-stepper/css/bs-stepper.min.css">
    <!-- dropzonejs -->
    <link rel="stylesheet" href="plugins/dropzone/min/dropzone.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote.min.js"></script>
</head>
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
          <h1 class="m-0">Ausbildungstypen</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Ausbildungstypen</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

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

    <!-- Modal für das Bearbeiten eines Ausbildungstyps -->
    <div class="modal fade" id="modal-ausbildung-edit">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Ausbildungstyp Bearbeiten</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="editAusbildungForm">
              <input type="hidden" id="edit_id" name="id"> <!-- ID des Ausbildungstyps -->
              <div class="form-group">
                <label for="edit_key_name">Key Name</label>
                <input type="text" class="form-control" id="edit_key_name" name="key_name" placeholder="Enter key name">
              </div>
              <div class="form-group">
                <label for="edit_display_name">Display Name</label>
                <input type="text" class="form-control" id="edit_display_name" name="display_name" placeholder="Enter display name">
              </div>
              <div class="form-group">
                <label for="edit_description">Beschreibung</label>
                <textarea class="form-control" id="edit_description" name="description" placeholder="Enter description"></textarea>
              </div>
            </form>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
            <button type="button" class="btn btn-primary" id="saveEditAusbildung">Speichern</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal für das Bearbeiten des Leitfadens -->
    <div class="modal fade" id="modal-ausbildung-leitfaden-edit">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Leitfaden Bearbeiten</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="editLeitfadenForm">
              <input type="hidden" id="edit_leitfaden_id" name="id"> <!-- ID des Ausbildungstyps -->
              <div class="form-group">
                <label for="edit_leitfaden">Leitfaden</label>
                <div id="edit_leitfaden" name="leitfaden"></div> <!-- Summernote div für den Leitfaden -->
              </div>
            </form>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
            <button type="button" class="btn btn-primary" id="saveEditLeitfaden">Speichern</button>
          </div>
        </div>
      </div>
    </div>

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
          <!-- Dynamisch geladene Daten -->
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

<script>
  $(document).ready(function() {
    // Initialisiere Summernote für den Leitfaden-Editor
    $('#edit_leitfaden').summernote({
      height: 200,
      placeholder: 'Geben Sie den Leitfaden ein...',
    });

    // Abrufen der Ausbildungstypen
    $.ajax({
      url: 'include/fetch_ausbildungstypen.php',
      type: 'GET',
      dataType: 'json',
      success: function(data) {
        const tableBody = $('#example1 tbody');
        tableBody.empty();
        data.forEach(function(ausbildung) {
          tableBody.append(`
            <tr>
              <td>${ausbildung.id}</td>
              <td>${ausbildung.key_name}</td>
              <td>${ausbildung.display_name}</td>
              <td>${ausbildung.description}</td>
              <td>
                <button class="btn btn-outline-secondary" data-id="${ausbildung.id}" onclick="editLeitfaden(${ausbildung.id})">Leitfaden bearbeiten</button>
                <button class="btn btn-outline-secondary" data-id="${ausbildung.id}" onclick="editAusbildung(${ausbildung.id})">Bearbeiten</button>
                <button class="btn btn-outline-danger" onclick="deleteAusbildungTyp(${ausbildung.id})">Löschen</button>
              </td>
            </tr>
          `);
        });
      }
    });

    // Leitfaden bearbeiten
    function editLeitfaden(id) {
      $.ajax({
        url: 'include/fetch_ausbildungstypen.php',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(data) {
          const ausbildung = data[0];
          $('#edit_leitfaden_id').val(ausbildung.id);
          $('#edit_leitfaden').summernote('code', ausbildung.leitfaden);
          $('#modal-ausbildung-leitfaden-edit').modal('show');
        }
      });
    }

    // Speichern der Änderungen im Leitfaden
    $('#saveEditLeitfaden').click(function() {
      const formData = new FormData(document.getElementById('editLeitfadenForm'));
      formData.append('leitfaden', $('#edit_leitfaden').summernote('code'));

      $.ajax({
        url: 'include/update_ausbildungstyp.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          alert('Leitfaden erfolgreich bearbeitet.');
          location.reload();
        },
        error: function(xhr, status, error) {
          alert('Fehler beim Bearbeiten des Leitfadens.');
        }
      });
    });
  });
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
    <!-- Summernote -->
    <script src="plugins/summernote/summernote-bs4.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 -->
    <script src="plugins/select2/js/select2.full.min.js"></script>
    <!-- Bootstrap4 Duallistbox -->
    <script src="plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
    <!-- InputMask -->
    <script src="plugins/moment/moment.min.js"></script>
    <script src="plugins/inputmask/jquery.inputmask.min.js"></script>
    <!-- date-range-picker -->
    <script src="plugins/daterangepicker/daterangepicker.js"></script>
    <!-- bootstrap color picker -->
    <script src="plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Bootstrap Switch -->
    <script src="plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
    <!-- BS-Stepper -->
    <script src="plugins/bs-stepper/js/bs-stepper.min.js"></script>
    <!-- dropzonejs -->
    <script src="plugins/dropzone/min/dropzone.min.js"></script>
</body>
</html>
