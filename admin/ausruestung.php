<?php
session_start(); // Ensure session is started

// CSRF-Token generieren und in der Session speichern
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>

<!DOCTYPE html>
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

    <!-- Ausbildungstyp erstellen Modal -->
    <div class="modal fade" id="modal-ausruestung-create">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Ausbildungstyp erstellen</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="createAusruestungForm">
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
                    <button type="button" class="btn btn-primary" id="saveAusruestungBtn">Speichern</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /.modal -->

    <!-- Modal für das Erstellen einer Kategorie -->
    <div class="modal fade" id="modal-kategorie-create">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Kategorie erstellen</h4>
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
                        <!-- Hidden Field für CSRF-Token -->
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
                    <button type="button" class="btn btn-success" id="saveNewCategory">Kategorie hinzufügen</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /.modal -->

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
                        <!-- Hidden Field für CSRF-Token -->
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
                    <button type="button" class="btn btn-primary" id="saveEditAusruestung">Speichern</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /.modal -->

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
    <!-- /.card-body -->
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
document.getElementById('saveAusruestungBtn').addEventListener('click', function() {
    const formData = new FormData(document.getElementById('createAusruestungForm'));

    fetch('include/create_ausruestung.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Ausbildungstyp erfolgreich erstellt');
            // Optionally, close the modal and reset the form
            $('#modal-ausruestung-create').modal('hide');
            document.getElementById('createAusruestungForm').reset();
            fetchAusruestung(); // Refresh the list
        } else {
            alert('Fehler: ' + (data.message || 'Unbekannter Fehler'));
        }
    })
    .catch(error => {
        alert('Ein Fehler ist aufgetreten: ' + error.message);
    });
});

document.getElementById('saveNewCategory').addEventListener('click', function() {
    const formData = new FormData(document.getElementById('createKategorieForm'));

    fetch('include/create_kategorie.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Kategorie erfolgreich erstellt');
            // Optionally, close the modal and reset the form
            $('#modal-kategorie-create').modal('hide');
            document.getElementById('createKategorieForm').reset();
            fetchAusruestung(); // Refresh the list
        } else {
            alert('Fehler: ' + (data.message || 'Unbekannter Fehler'));
        }
    })
    .catch(error => {
        alert('Ein Fehler ist aufgetreten: ' + error.message);
    });
});

document.getElementById('saveEditAusruestung').addEventListener('click', function() {
    const formData = new FormData(document.getElementById('editAusruestungForm'));

    fetch('include/update_ausruestung.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Ausrüstung erfolgreich bearbeitet');
            // Optionally, close the modal and reset the form
            $('#modal-ausruestung-edit').modal('hide');
            document.getElementById('editAusruestungForm').reset();
            fetchAusruestung(); // Refresh the list
        } else {
            alert('Fehler: ' + (data.message || 'Unbekannter Fehler'));
        }
    })
    .catch(error => {
        alert('Ein Fehler ist aufgetreten: ' + error.message);
    });
});

function fetchAusruestung() {
    const formData = new FormData();
    formData.append('csrf_token', '<?php echo $csrf_token; ?>');

    fetch('include/fetch_ausruestungstypen.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const tbody = document.querySelector('#example1 tbody');
            tbody.innerHTML = ''; // Clear existing rows

            data.ausruestung.forEach((item, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${item.key_name}</td>
                    <td>${item.display_name}</td>
                    <td>${item.description}</td>
                    <td>
                        <button class="btn btn-info btn-sm" onclick="editAusruestung(${item.id})">Bearbeiten</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteAusruestung(${item.id})">Löschen</button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        } else {
            alert('Fehler: ' + (data.message || 'Unbekannter Fehler'));
        }
    })
    .catch(error => {
        alert('Ein Fehler ist aufgetreten: ' + error.message);
    });
}

// Call the function to fetch Ausruestung
fetchAusruestung();

function editAusruestung(id) {
    // Fetch the Ausruestung data and populate the edit form
    const formData = new FormData();
    formData.append('csrf_token', '<?php echo $csrf_token; ?>');
    formData.append('id', id);

    fetch('include/fetch_ausruestungstypen.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const item = data.ausruestung;
            if (item) {
                document.getElementById('edit_id').value = item.id;
                document.getElementById('edit_key_name').value = item.key_name;
                document.getElementById('edit_display_name').value = item.display_name;
                document.getElementById('edit_description').value = item.description;
                $('#modal-ausruestung-edit').modal('show');
            } else {
                alert('Fehler: Ausruestung nicht gefunden.');
            }
        } else {
            alert('Fehler: ' + (data.message || 'Unbekannter Fehler'));
        }
    })
    .catch(error => {
        alert('Ein Fehler ist aufgetreten: ' + error.message);
    });
}

function deleteAusruestung(id) {
    if (!confirm('Sind Sie sicher, dass Sie diese Ausruestung löschen möchten?')) {
        return;
    }

    const formData = new FormData();
    formData.append('csrf_token', '<?php echo $csrf_token; ?>');
    formData.append('id', id);

    fetch('include/delete_ausruestung.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Ausruestung erfolgreich gelöscht');
            fetchAusruestung(); // Refresh the list
        } else {
            alert('Fehler: ' + (data.message || 'Unbekannter Fehler'));
        }
    })
    .catch(error => {
        alert('Ein Fehler ist aufgetreten: ' + error.message);
    });
}
</script>

</body>
</html>
