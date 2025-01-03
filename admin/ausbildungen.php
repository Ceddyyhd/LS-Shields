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
                <h4 class="modal-title">Ausbildungstyp Ändern</h4>
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
  $(document).ready(function() {
    // AJAX-Anfrage zum Abrufen der Ausbildungstypen direkt beim Laden der Seite
    $.ajax({
        url: 'include/fetch_ausbildungstypen.php', // URL für das Abrufen der Daten
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            console.log('Daten erhalten:', data); // Logge die erhaltenen Daten in der Konsole
            if (data && data.length > 0) {
                // Wenn Daten vorhanden sind, befülle die Tabelle oder Modal
                const tableBody = $('#example1 tbody'); // Beispiel: ID der Tabelle, in der die Daten angezeigt werden
                tableBody.empty(); // Leere das Tabellenbody, bevor neue Daten hinzugefügt werden

                // Daten durchlaufen und in die Tabelle einfügen
                data.forEach(function(ausbildung) {
                    tableBody.append(`
                        <tr>
                            <td>${ausbildung.id}</td>
                            <td>${ausbildung.key_name}</td>
                            <td>${ausbildung.display_name}</td>
                            <td>${ausbildung.description}</td>
                            <td>
                          <?php if (isset($_SESSION['permissions']['ausbildungstyp_update']) && $_SESSION['permissions']['ausbildungstyp_update']): ?>
                            <button class="btn btn-outline-secondary" data-id="${ausbildung.id}">Bearbeiten</button>
                          <?php endif; ?>
                          <?php if (isset($_SESSION['permissions']['ausbildungstyp_remove']) && $_SESSION['permissions']['ausbildungstyp_remove']): ?>
                              <button class="btn btn-outline-danger" onclick="deleteAusbildungTyp(${ausbildung.id})">Löschen</button>
                          <?php endif; ?>
                          </td>
                        </tr>
                    `);
                });
            } else {
                alert('Keine Ausbildungstypen gefunden.');
            }
        },
        error: function(xhr, status, error) {
            console.error('Fehler beim Abrufen der Ausbildungstypen:', error); // Fehlerlog
            alert('Fehler beim Abrufen der Ausbildungstypen.');
        }
    });
});

// Wenn der Bearbeiten-Button geklickt wird
$(document).on('click', '.btn-outline-secondary', function() {
    const id = $(this).data('id'); // Hole die ID des zu bearbeitenden Ausbildungstyps
    
    // AJAX-Anfrage, um die Daten des Ausbildungstyps abzurufen
    $.ajax({
        url: 'include/fetch_ausbildungstypen.php',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(data) {
            if (data && data.length > 0) {
                const ausbildung = data[0]; // Nur ein Element zurück, da wir nach ID filtern

                // Setze die Modal-Felder mit den Daten des Ausbildungstyps
                $('#edit_id').val(ausbildung.id); // ID in hidden input
                $('#edit_key_name').val(ausbildung.key_name); // Key Name
                $('#edit_display_name').val(ausbildung.display_name); // Display Name
                $('#edit_description').val(ausbildung.description); // Beschreibung
                
                // Zeige das Bearbeitungsmodal an
                $('#modal-ausbildung-edit').modal('show');
            } else {
                alert('Daten konnten nicht geladen werden.');
            }
        },
        error: function(xhr, status, error) {
            console.error('Fehler beim Abrufen der Ausbildungstypen:', error);
            alert('Fehler beim Abrufen der Ausbildungstypen.');
        }
    });
});

// Wenn der "Speichern"-Button im Bearbeitungsmodal geklickt wird
$('#saveEditAusbildung').click(function() {
    const formData = new FormData(document.getElementById('editAusbildungForm'));

    $.ajax({
        url: 'include/update_ausbildungstyp.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            alert('Ausbildungstyp erfolgreich bearbeitet.');
            location.reload(); // Seite neu laden, um die Änderungen anzuzeigen
        },
        error: function(xhr, status, error) {
            console.error('Fehler beim Bearbeiten:', error);
            alert('Fehler beim Bearbeiten des Ausbildungstyps.');
        }
    });
});

  // Löschen-Funktion
  function deleteAusbildungTyp(id) {
    if (confirm('Möchten Sie diesen Ausbildungstyp wirklich löschen? (Er wird archiviert)')) {
        $.ajax({
            url: 'include/delete_ausbildungstyp.php',
            type: 'POST',
            data: { id: id },
            success: function(response) {
                alert('Ausbildungstyp archiviert');
                location.reload(); // Seite neu laden, um die Änderungen anzuzeigen
            },
            error: function(xhr, status, error) {
                console.error('Fehler beim Archivieren:', xhr.responseText);
                alert('Fehler beim Archivieren des Ausbildungstyps.');
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
