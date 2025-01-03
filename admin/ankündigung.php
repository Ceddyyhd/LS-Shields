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
  <?php if (isset($_SESSION['permissions']['ankuendigung_create']) && $_SESSION['permissions']['ankuendigung_create']): ?>
  <div class="card-header">
    <h3 class="card-title">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-ankuendigung-create">
            Ankündigung erstellen
        </button>
    </h3>
  </div>
<?php endif; ?>

<!-- Modal zum Erstellen einer Ankündigung -->
<div class="modal fade" id="modal-ankuendigung-create">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?php echo htmlspecialchars($user_name); ?></h4>  <!-- Benutzername einfügen -->
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="createAnkuendigungForm">
                    <div class="form-group">
                        <label for="key_name">Titel</label>
                        <input type="text" class="form-control" id="key_name" name="key_name" placeholder="Titel eingeben">
                    </div>
                    <div class="form-group">
                        <label for="description">Beschreibung</label>
                        <textarea class="form-control" id="description" name="description" placeholder="Beschreibung eingeben"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Priorität</label>
                        <select class="custom-select" name="prioritaet">
                            <option value="Low">Low</option>
                            <option value="Mid">Mid</option>
                            <option value="High">High</option>
                        </select>
                    </div>
                    <!-- Keine Notwendigkeit für 'created_by' im Modal, es wird automatisch gesetzt -->
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
                <button type="button" class="btn btn-primary" id="saveCreateAnkuendigung">Speichern</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal zum Bearbeiten einer Ankündigung -->
<div class="modal fade" id="modal-ankuendigung-bearbeiten">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Ankündigung Bearbeiten</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editAnkuendigungForm">
                    <input type="hidden" id="edit_id" name="id"> <!-- Versteckte ID für das Bearbeiten -->
                    <div class="form-group">
                        <label for="edit_key_name">Titel</label>
                        <input type="text" class="form-control" id="edit_key_name" name="key_name" placeholder="Titel eingeben">
                    </div>
                    <div class="form-group">
                        <label for="edit_description">Beschreibung</label>
                        <textarea class="form-control" id="edit_description" name="description" placeholder="Beschreibung eingeben"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Priorität</label>
                        <select class="custom-select" name="prioritaet" id="edit_prioritaet">
                            <option value="Low">Low</option>
                            <option value="Mid">Mid</option>
                            <option value="High">High</option>
                        </select>
                    </div>
                    <!-- Der Name des Bearbeiters wird hier gesetzt -->
                    <div class="form-group">
                        <label for="edit_key_name">Bearbeitet von</label>
                        <input type="text" class="form-control" id="edit_key_name" name="key_name" value="name aus db">
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
                <button type="button" class="btn btn-primary" id="saveEditAnkuendigung">Speichern</button>
            </div>
        </div>
    </div>
</div>


<!-- Tabelle zur Anzeige der Ankündigungen -->
<table id="example1" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Titel</th>
            <th>Beschreibung</th>
            <th>Priorität</th>
            <th>Aktion</th>
        </tr>
    </thead>
    <tbody>
        <!-- Daten werden hier dynamisch geladen -->
    </tbody>
</table>

<!-- jQuery-Skript -->
<script>
$(document).ready(function() {
    // Ankündigungen abrufen
    $.ajax({
        url: 'include/fetch_ankuendigung.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data && data.length > 0) {
                const tableBody = $('#example1 tbody');
                tableBody.empty();
                data.forEach(function(ankuendigung) {
                    tableBody.append(`
                        <tr>
                            <td>${ankuendigung.id}</td>
                            <td>${ankuendigung.key_name}</td>
                            <td>${ankuendigung.description}</td>
                            <td>${ankuendigung.prioritaet}</td>
                            <td>
                                <button class="btn btn-outline-secondary" data-id="${ankuendigung.id}">Bearbeiten</button>
                                <button class="btn btn-outline-danger" onclick="deleteAnkuendigung(${ankuendigung.id})">Löschen</button>
                            </td>
                        </tr>
                    `);
                });
            } else {
                alert('Keine Ankündigungen gefunden.');
            }
        },
        error: function() {
            alert('Fehler beim Abrufen der Ankündigungen.');
        }
    });

    // Wenn der Bearbeiten-Button geklickt wird
    $(document).on('click', '.btn-outline-secondary', function() {
        const id = $(this).data('id'); // Hole die ID der zu bearbeitenden Ankündigung
        
        // AJAX-Anfrage, um die Daten der Ankündigung abzurufen
        $.ajax({
            url: 'include/fetch_ankuendigung.php',
            type: 'GET',
            data: { id: id },
            dataType: 'json',
            success: function(data) {
                if (data && data.length > 0) {
                    const ankuendigung = data[0]; // Nur ein Element zurück, da wir nach ID filtern

                    // Setze die Modal-Felder mit den Daten der Ankündigung
                    $('#edit_id').val(ankuendigung.id); // ID in hidden input
                    $('#edit_key_name').val(ankuendigung.key_name); // Key Name
                    $('#edit_description').val(ankuendigung.description); // Beschreibung
                    $('#edit_prioritaet').val(ankuendigung.prioritaet); // Priorität
                    
                    // Zeige das Bearbeitungsmodal an
                    $('#modal-ankuendigung-bearbeiten').modal('show');
                } else {
                    alert('Daten konnten nicht geladen werden.');
                }
            },
            error: function() {
                alert('Fehler beim Abrufen der Ankündigung.');
            }
        });
    });

    // Wenn der "Speichern"-Button im Bearbeitungsmodal geklickt wird
    $('#saveEditAnkuendigung').click(function() {
        const formData = new FormData(document.getElementById('editAnkuendigungForm'));

        $.ajax({
            url: 'include/update_ankuendigung.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                alert(response.message);
                location.reload(); // Seite neu laden, um die Änderungen anzuzeigen
            },
            error: function() {
                alert('Fehler beim Bearbeiten der Ankündigung.');
            }
        });
    });

    // Löschen einer Ankündigung
    window.deleteAnkuendigung = function(id) {
        if (confirm('Möchten Sie diese Ankündigung wirklich löschen?')) {
            $.ajax({
                url: 'include/delete_ankuendigung.php',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    alert(response.message);
                    location.reload(); // Seite neu laden, um die Änderungen anzuzeigen
                },
                error: function() {
                    alert('Fehler beim Löschen der Ankündigung.');
                }
            });
        }
    };

    // Erstellen einer neuen Ankündigung
    $('#saveCreateAnkuendigung').click(function() {
        const formData = $('#createAnkuendigungForm').serialize(); // Formulardaten sammeln

        $.ajax({
            url: 'include/create_ankuendigung.php', // PHP-Datei zum Erstellen der Ankündigung
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Ankündigung erfolgreich erstellt.');
                    $('#modal-ankuendigung-create').modal('hide'); // Modal schließen
                    location.reload(); // Seite neu laden, um die neue Ankündigung anzuzeigen
                } else {
                    alert('Fehler beim Erstellen der Ankündigung: ' + response.error);
                }
            },
            error: function() {
                alert('Fehler beim Erstellen der Ankündigung.');
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
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>
