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
  <?php if (isset($_SESSION['permissions']['ausruestung_create']) && $_SESSION['permissions']['ausruestung_create']): ?>
  <div class="card-header">
    <h3 class="card-title">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-ausruestung-create">
            Ausbildungstyp erstellen
        </button>
    </h3>
  </div>
<?php endif; ?>

<!-- Modal für das Erstellen eines neuen Ausrüstungstyps -->
<div class="modal fade" id="modal-ausruestung-create">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Ausrüstung Erstellen</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="createAusruestungForm">
                    <div class="form-group">
                        <label for="key_name">Key Name</label>
                        <input type="text" class="form-control" id="key_name" name="key_name" placeholder="Enter key name">
                    </div>
                    <div class="form-group">
                        <label for="display_name">Display Name</label>
                        <input type="text" class="form-control" id="display_name" name="display_name" placeholder="Enter display name">
                    </div>
                    <div class="form-group">
                        <label for="category">Kategorie</label>
                        <select class="form-control" id="category" name="category">
                            <!-- Kategorien werden hier dynamisch geladen -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="description">Beschreibung</label>
                        <textarea class="form-control" id="description" name="description" placeholder="Enter description"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveEditAusruestung">Speichern</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal für das Bearbeiten eines Ausrüstungstyps -->
<div class="modal fade" id="modal-ausruestung-edit">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Ausrüstung Ändern</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editAusruestungForm">
                    <input type="hidden" id="edit_id" name="id"> <!-- ID des Ausrüstungstyps -->
                    <div class="form-group">
                        <label for="edit_key_name">Key Name</label>
                        <input type="text" class="form-control" id="edit_key_name" name="key_name" placeholder="Enter key name">
                    </div>
                    <div class="form-group">
                        <label for="edit_display_name">Display Name</label>
                        <input type="text" class="form-control" id="edit_display_name" name="display_name" placeholder="Enter display name">
                    </div>
                    <div class="form-group">
                        <label for="edit_category">Kategorie</label>
                        <select class="form-control" id="edit_category" name="category">
                            <!-- Kategorien werden hier dynamisch geladen -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_description">Beschreibung</label>
                        <textarea class="form-control" id="edit_description" name="description" placeholder="Enter description"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
                <button type="button" class="btn btn-primary" id="saveEditAusruestung">Speichern</button>
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
    // AJAX-Anfrage zum Abrufen der Ausrüstungstypen direkt beim Laden der Seite
    $.ajax({
        url: 'include/fetch_ausruestungstypen.php', // URL für das Abrufen der Daten
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            console.log('Daten erhalten:', data); // Logge die erhaltenen Daten in der Konsole
            if (data && data.length > 0) {
                // Wenn Daten vorhanden sind, befülle die Tabelle
                const tableBody = $('#example1 tbody'); // Beispiel: ID der Tabelle, in der die Daten angezeigt werden
                tableBody.empty(); // Leere das Tabellenbody, bevor neue Daten hinzugefügt werden

                // Daten durchlaufen und in die Tabelle einfügen
                data.forEach(function(ausruestung) {
                    tableBody.append(`
                        <tr>
                            <td>${ausruestung.id}</td>
                            <td>${ausruestung.key_name}</td>
                            <td>${ausruestung.display_name}</td>
                            <td>${ausruestung.description}</td>
                            <td>
                              <!-- Bearbeiten-Button nur anzeigen, wenn die Berechtigung vorhanden ist -->
                              ${ausruestung.can_edit ? '<button class="btn btn-outline-secondary" data-id="' + ausruestung.id + '">Bearbeiten</button>' : ''}
                              <!-- Löschen-Button nur anzeigen, wenn die Berechtigung vorhanden ist -->
                              ${ausruestung.can_delete ? '<button class="btn btn-outline-danger" onclick="deleteAusruestungTyp(' + ausruestung.id + ')">Löschen</button>' : ''}
                            </td>
                        </tr>
                    `);
                });
            } else {
                alert('Keine Ausrüstungstypen gefunden.');
            }
        },
        error: function(xhr, status, error) {
            console.error('Fehler beim Abrufen der Ausrüstungstypen:', error); // Fehlerlog
            alert('Fehler beim Abrufen der Ausrüstungstypen.');
        }
    });

    // AJAX-Anfrage zum Abrufen der Kategorien und Befüllen des Dropdowns
    $.ajax({
        url: 'include/fetch_kategorien.php', // URL für das Abrufen der Kategorien
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            const categorySelect = $('#category, #edit_category'); // Beide Dropdowns für Erstellen und Bearbeiten
            categorySelect.empty(); // Leere das Dropdown, bevor neue Kategorien hinzugefügt werden

            // Kategorien durchlaufen und im Dropdown-Menü einfügen
            data.forEach(function(category) {
                categorySelect.append(`<option value="${category.category}">${category.category}</option>`);
            });
        },
        error: function(xhr, status, error) {
            console.error('Fehler beim Abrufen der Kategorien:', error); // Fehlerlog
            alert('Fehler beim Abrufen der Kategorien.');
        }
    });
});

// Wenn der Bearbeiten-Button geklickt wird
$(document).on('click', '.btn-outline-secondary', function() {
    const id = $(this).data('id'); // Hole die ID des zu bearbeitenden Ausrüstungstyps
    
    // AJAX-Anfrage, um die Daten des Ausrüstungstyps abzurufen
    $.ajax({
        url: 'include/fetch_ausruestungstypen.php',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(data) {
            if (data && data.length > 0) {
                const ausruestung = data[0]; // Nur ein Element zurück, da wir nach ID filtern

                // Setze die Modal-Felder mit den Daten des Ausrüstungstyps
                $('#edit_id').val(ausruestung.id); // ID in hidden input
                $('#edit_key_name').val(ausruestung.key_name); // Key Name
                $('#edit_display_name').val(ausruestung.display_name); // Display Name
                $('#edit_description').val(ausruestung.description); // Beschreibung
                
                // Setze die Kategorie im Bearbeitungsmodal
                $('#edit_category').val(ausruestung.category); // Setze die Kategorie im Dropdown
                
                // Zeige das Bearbeitungsmodal an
                $('#modal-ausruestung-edit').modal('show');
            } else {
                alert('Daten konnten nicht geladen werden.');
            }
        },
        error: function(xhr, status, error) {
            console.error('Fehler beim Abrufen der Ausrüstungstypen:', error);
            alert('Fehler beim Abrufen der Ausrüstungstypen.');
        }
    });
});
// Wenn der "Speichern"-Button im Erstell Modal geklickt wird
$('#saveAusruestung').click(function() {
        const formData = new FormData(document.getElementById('createAusruestungForm'));

        $.ajax({
            url: 'include/create_ausruestungstyp.php', // URL zum Speichern des neuen Ausrüstungstyps
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                alert('Ausrüstungstyp erfolgreich erstellt.');
                location.reload(); // Seite neu laden, um die neue Ausrüstung anzuzeigen
            },
            error: function(xhr, status, error) {
                console.error('Fehler beim Erstellen:', error);
                alert('Fehler beim Erstellen des Ausrüstungstyps.');
            }
    });
});
// Wenn der "Speichern"-Button im Bearbeitungsmodal geklickt wird
$('#saveEditAusruestung').click(function() {
    const formData = new FormData(document.getElementById('editAusruestungForm'));

    $.ajax({
        url: 'include/update_ausruestungstyp.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            alert('Ausrüstungstyp erfolgreich bearbeitet.');
            location.reload(); // Seite neu laden, um die Änderungen anzuzeigen
        },
        error: function(xhr, status, error) {
            console.error('Fehler beim Bearbeiten:', error);
            alert('Fehler beim Bearbeiten des Ausrüstungstyps.');
        }
    });
});

// Löschen-Funktion
function deleteAusruestungTyp(id) {
    if (confirm('Möchten Sie diesen Ausrüstungstyp wirklich löschen? (Er wird archiviert)')) {
        $.ajax({
            url: 'include/delete_ausruestungstyp.php',
            type: 'POST',
            data: { id: id },
            success: function(response) {
                alert('Ausrüstungstyp archiviert');
                location.reload(); // Seite neu laden, um die Änderungen anzuzeigen
            },
            error: function(xhr, status, error) {
                console.error('Fehler beim Archivieren:', xhr.responseText);
                alert('Fehler beim Archivieren des Ausrüstungstyps.');
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
