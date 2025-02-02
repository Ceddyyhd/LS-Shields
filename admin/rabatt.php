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
  <?php if (isset($_SESSION['permissions']['rabatt_create']) && $_SESSION['permissions']['rabatt_create']): ?>
  <div class="card-header">
    <h3 class="card-title">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-rabatt-create">
            Ankündigung erstellen
        </button>
    </h3>
  </div>
<?php endif; ?>

<!-- Modal zum Erstellen eines Rabatts -->
<div class="modal fade" id="modal-rabatt-create">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Rabatt Erstellen</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="createRabattForm">
                    <div class="form-group">
                        <label for="display_name">Firma</label>
                        <input type="text" class="form-control" id="display_name" name="display_name" placeholder="Firma eingeben">
                    </div>
                    <div class="form-group">
                        <label for="description">Beschreibung</label>
                        <textarea class="form-control" id="description" name="description" placeholder="Beschreibung eingeben"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="rabatt_percent">Rabatt in %</label>
                        <input type="number" class="form-control" id="rabatt_percent" name="rabatt_percent" placeholder="Rabatt in % eingeben">
                    </div>
                    <input type="hidden" class="form-control" id="created_by" name="created_by" value="<?php echo htmlspecialchars($user_name); ?>" />
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
                <button type="button" class="btn btn-primary" id="saveCreateRabatt">Speichern</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal zum Bearbeiten eines Rabatts -->
<div class="modal fade" id="modal-rabatt-bearbeiten">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Rabatt Bearbeiten</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editRabattForm">
                    <!-- Firma -->
                    <div class="form-group">
                        <label for="edit_display_name">Firma</label>
                        <input type="text" class="form-control" id="edit_display_name" name="display_name" placeholder="Titel eingeben">
                    </div>
                    <!-- Beschreibung -->
                    <div class="form-group">
                        <label for="edit_description">Beschreibung</label>
                        <textarea class="form-control" id="edit_description" name="description" placeholder="Beschreibung eingeben"></textarea>
                    </div>
                    <!-- Rabatt in % -->
                    <div class="form-group">
                        <label for="edit_rabatt_percent">Rabatt in %</label>
                        <input type="number" class="form-control" id="edit_rabatt_percent" name="rabatt_percent" placeholder="Rabatt in % eingeben">
                    </div>
                    <!-- ID des Rabatts -->
                    <input type="hidden" id="edit_id" name="id">
                    <!-- Benutzername für den Bearbeiter -->
                    <input type="hidden" class="form-control" id="created_by" name="created_by" value="<?php echo htmlspecialchars($user_name); ?>" />
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
                <button type="button" class="btn btn-primary" id="saveEditRabatt">Speichern</button>
            </div>
        </div>
    </div>
</div>


<!-- Tabelle zur Anzeige der Rabatte -->
<table id="example1" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Firma</th>
            <th>Beschreibung</th>
            <th>Rabatt in %</th>
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
    // Rabatte abrufen
    $.ajax({
        url: 'include/fetch_rabatt.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data && data.length > 0) {
                const tableBody = $('#example1 tbody');
                tableBody.empty();  // Tabelle leeren, bevor neue Einträge hinzugefügt werden
                data.forEach(function(rabatt) {
                    tableBody.append(`
                        <tr>
                            <td>${rabatt.id}</td>
                            <td>${rabatt.display_name}</td>
                            <td>${rabatt.description}</td>
                            <td>${rabatt.rabatt_percent}%</td>
                            <td>
                                <button class="btn btn-outline-secondary" data-id="${rabatt.id}">Bearbeiten</button>
                                <button class="btn btn-outline-danger" onclick="deleteRabatt(${rabatt.id})">Löschen</button>
                            </td>
                        </tr>
                    `);
                });
            }
        },
        error: function() {
            alert('Fehler beim Abrufen der Rabatte');
        }
    });

    // Erstellen eines neuen Rabatts
    $('#saveCreateRabatt').click(function() {
        const formData = $('#createRabattForm').serialize(); // Formulardaten sammeln

        $.ajax({
            url: 'include/create_rabatt.php', // PHP-Datei zum Erstellen des Rabatts
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#modal-rabatt-create').modal('hide'); // Modal schließen
                    location.reload(); // Seite neu laden, um den neuen Rabatt anzuzeigen
                } else {
                    alert('Fehler beim Erstellen des Rabatts');
                }
            },
            error: function() {
                alert('Fehler beim Erstellen des Rabatts');
            }
        });
    });

    // Wenn der Bearbeiten-Button geklickt wird
    $(document).on('click', '.btn-outline-secondary', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: 'include/fetch_rabatt.php',
            type: 'GET',
            data: { id: id },
            dataType: 'json',
            success: function(data) {
                if (data) {
                    const rabatt = data[0]; // Nur ein Element zurück

                    $('#edit_id').val(rabatt.id);
                    $('#edit_display_name').val(rabatt.display_name);
                    $('#edit_description').val(rabatt.description);
                    $('#edit_rabatt_percent').val(rabatt.rabatt_percent);

                    $('#modal-rabatt-bearbeiten').modal('show');
                }
            },
            error: function() {
                alert('Fehler beim Abrufen des Rabatts');
            }
        });
    });

    // Speichern der Bearbeitung
    $('#saveEditRabatt').click(function() {
    const formData = new FormData(document.getElementById('editRabattForm'));

    $.ajax({
        url: 'include/update_rabatt.php',  // PHP-Datei zum Bearbeiten des Rabatts
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                $('#modal-rabatt-bearbeiten').modal('hide');
                location.reload();
            } else {
                alert('Fehler beim Bearbeiten des Rabatts');
            }
        },
        error: function() {
            alert('Fehler beim Bearbeiten des Rabatts');
        }
    });
});

    // Löschen eines Rabatts
    window.deleteRabatt = function(id) {
        if (confirm('Möchten Sie diesen Rabatt wirklich löschen?')) {
            $.ajax({
                url: 'include/delete_rabatt.php',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    alert(response.message);
                    location.reload(); // Seite neu laden
                },
                error: function() {
                    alert('Fehler beim Löschen des Rabatts');
                }
            });
        }
    };
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
