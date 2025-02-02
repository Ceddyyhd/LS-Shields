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
        <h3 class="card-title">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-kategorie-create">
                Kategorie Erstellen
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
                    <input type="hidden" id="edit_id" name="id">
                    <input type="hidden" id="edit_user_name" name="user_name" value="<?php echo $_SESSION['username']; ?>"> <!-- Benutzername aus der Session -->
                    <input type="hidden" id="edit_editor_name" name="editor_name">

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

<!-- Modal für Historie -->
<div class="modal" id="modal-history">
    <div class="modal-dialog" style="max-width: 90%; margin: 30px auto;">
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
                        <td>${ausruestung.stock}</td>
                        <td>
                            ${ausruestung.can_edit ? '<button class="btn btn-outline-secondary" data-id="' + ausruestung.id + '" data-keyname="' + ausruestung.key_name + '" data-displayname="' + ausruestung.display_name + '" data-category="' + ausruestung.category + '" data-description="' + ausruestung.description + '" data-stock="' + ausruestung.stock + '" onclick="openEditModal(this)">Bearbeiten</button>' : ''}
                            ${ausruestung.can_delete ? '<button class="btn btn-outline-danger" onclick="deleteAusruestungTyp(' + ausruestung.id + ')">Löschen</button>' : ''}
                            <button class="btn btn-outline-info history-button" data-id="${ausruestung.key_name}">Historie</button> <!-- Historie-Button -->
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
        $('#edit_stock').val(stock); // Bestandswert setzen

        loadCategories(category);
        $('#modal-ausruestung-edit').modal('show');
    }

    // Funktion zum Laden der Kategorien
    function loadCategories(selectedCategory) {
        $.ajax({
            url: 'include/fetch_kategorien.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                const categorySelect = $('#category, #edit_category'); // Beide Selects ansprechen
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

    // Kategorie hinzufügen
    $('#saveNewCategory').click(function() {
        const newCategoryName = $('#new_category_name').val();

        if (newCategoryName) {
            $.ajax({
                url: 'include/create_category.php', // Hier wird die PHP-Datei zum Erstellen der Kategorie aufgerufen
                type: 'POST',
                data: { name: newCategoryName },
                success: function(response) {
                    alert('Kategorie erfolgreich hinzugefügt.');
                    loadCategories(); // Lade die Kategorien nach dem Hinzufügen neu
                    $('#modal-kategorie-create').modal('hide');
                },
                error: function(xhr, status, error) {
                    alert('Fehler beim Hinzufügen der Kategorie.');
                }
            });
        } else {
            alert('Bitte geben Sie einen Kategorienamen ein.');
        }
    });

    // Historie anzeigen
    $(document).on('click', '.history-button', function() {
        const ausruestungId = $(this).data('id');
        openHistoryModal(ausruestungId);
    });

    function openHistoryModal(ausruestungId) {
        $.ajax({
            url: 'include/fetch_ausruestung_history.php',
            type: 'GET',
            data: { id: ausruestungId },
            dataType: 'json',
            success: function(data) {
                const historyTableBody = $("#historyTable tbody");
                historyTableBody.empty();

                // Wenn keine Historie gefunden wird
                if (data.success === false) {
                    historyTableBody.append(`
                        <tr><td colspan="4">${data.message}</td></tr>
                    `);
                } else {
                    // Historie-Daten einfügen
                    data.forEach(function(historyEntry) {
                        historyTableBody.append(`
                            <tr>
                                <td>${historyEntry.timestamp}</td>
                                <td>${historyEntry.action}</td>
                                <td>${historyEntry.stock_change}</td>
                                <td>${historyEntry.editor_name}</td>
                            </tr>
                        `);
                    });
                }

                // Modal anzeigen
                $("#modal-history").modal("show");
            },
            error: function(xhr, status, error) {
                alert("Fehler beim Abrufen der Historie.");
            }
        });
    }

    // Speichern der Änderungen für Ausrüstungen
    $('#saveAusruestung').click(function() {
        const formData = new FormData(document.getElementById('createAusruestungForm'));

        $.ajax({
            url: 'include/create_ausruestungstyp.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                alert('Ausrüstungstyp erfolgreich erstellt.');
                location.reload();
            },
            error: function(xhr, status, error) {
                alert('Fehler beim Erstellen des Ausrüstungstyps.');
            }
        });
    });

    // Speichern der Änderungen für den bearbeiteten Ausrüstungsgegenstand
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
