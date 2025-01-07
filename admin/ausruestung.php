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
<!-- JavaScript Section -->
<script src="https://cdn.datatables.net/1.11.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.6/js/dataTables.bootstrap4.min.js"></script>
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
                <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
                <button type="button" class="btn btn-primary" id="saveAusruestung">Speichern</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal für das Erstellen einer neuen Kategorie -->
<div class="modal fade" id="modal-kategorie-create">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Kategorie Erstellen</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="createKategorieForm">
                    <div class="form-group">
                        <label for="new_category_name">Kategorie Name</label>
                        <input type="text" class="form-control" id="new_category_name" name="new_category_name" placeholder="Kategorie hinzufügen">
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
                <button type="button" class="btn btn-success" id="saveNewCategory">Kategorie hinzufügen</button>
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
        <input type="hidden" id="edit_user_name" name="user_name"> <!-- Verstecktes Input für Benutzernamen -->
        <input type="hidden" id="edit_id" name="id">
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
        <div class="form-group">
            <label for="edit_stock">Bestand</label>
            <input type="number" class="form-control" id="edit_stock" name="stock" placeholder="Enter stock amount">
        </div>
        <div class="form-group">
            <label for="note">Notiz zur Bestandsänderung</label>
            <textarea class="form-control" id="note" name="note" placeholder="Geben Sie eine Notiz zur Bestandsänderung ein"></textarea>
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
        <th>Stock</th>
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
        <th>Stock</th>
        <th>Aktion</th>
      </tr>
    </tfoot>
  </table>
    </div>
  </div>
</div>


<!-- Modal für Historie -->
<div class="modal" id="modal-history">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Historie</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table id="historyTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Datum</th>
                            <th>Aktion</th>
                            <th>Bestandsänderung</th>
                            <th>Benutzer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Historie wird hier eingefügt -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    // Daten für Ausrüstungen laden
    $.ajax({
        url: 'include/fetch_ausruestungstypen.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            const tableBody = $('#example1 tbody');
            tableBody.empty();

            data.forEach(function(ausruestung) {
                tableBody.append(`
                    <tr>
                        <td>${ausruestung.id}</td>
                        <td>${ausruestung.key_name}</td>
                        <td>${ausruestung.display_name}</td>
                        <td>${ausruestung.description}</td>
                        <td>${ausruestung.stock}</td> <!-- Bestand -->
                        <td>
                            ${ausruestung.can_edit ? '<button class="btn btn-outline-secondary" data-id="' + ausruestung.id + '" data-keyname="' + ausruestung.key_name + '" data-displayname="' + ausruestung.display_name + '" data-category="' + ausruestung.category + '" data-description="' + ausruestung.description + '" onclick="openEditModal(this)">Bearbeiten</button>' : ''}
                            ${ausruestung.can_delete ? '<button class="btn btn-outline-danger" onclick="deleteAusruestungTyp(' + ausruestung.id + ')">Löschen</button>' : ''}
                            <button class="btn btn-outline-info history-button" data-id="${ausruestung.id}">Historie</button> <!-- Historie-Button -->
                        </td>
                    </tr>
                `);
            });
        },
        error: function(xhr, status, error) {
            alert('Fehler beim Abrufen der Ausrüstungstypen.');
        }
    });

    // Funktion zum Öffnen des Bearbeitungs-Modals und Laden der Daten
    window.openEditModal = function(button) {
        const id = $(button).data('id');
        const keyName = $(button).data('keyname');
        const displayName = $(button).data('displayname');
        const category = $(button).data('category');
        const description = $(button).data('description');
        const stock = $(button).data('stock');

        $('#edit_id').val(id);
        $('#edit_key_name').val(keyName);
        $('#edit_display_name').val(displayName);
        $('#edit_description').val(description);
        $('#edit_stock').val(stock);

        loadCategories(category);
        
        // Hier den Benutzernamen aus der Session setzen und in das versteckte Input eintragen
        const userName = '<?php echo $_SESSION["user_name"]; ?>'; // PHP-Session-Wert einfügen
        $('#edit_user_name').val(userName);

        $('#modal-ausruestung-edit').modal('show');
    }

    // Funktion zum Laden der Kategorien
    function loadCategories(selectedCategory) {
        $.ajax({
            url: 'include/fetch_kategorien.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                const categorySelect = $('#edit_category');
                categorySelect.empty();

                data.forEach(function(category) {
                    const isSelected = category.name === selectedCategory ? 'selected' : '';
                    categorySelect.append(`<option value="${category.name}" ${isSelected}>${category.name}</option>`);
                });
            },
            error: function() {
                alert('Fehler beim Laden der Kategorien.');
            }
        });
    }

    // Speichern der Änderungen
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
                location.reload();
            },
            error: function(xhr, status, error) {
                alert('Fehler beim Bearbeiten des Ausrüstungstyps.');
            }
        });
    });
});

</script>





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
