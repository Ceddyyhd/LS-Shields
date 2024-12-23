<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<?php include 'include/header.php'; ?>

<body class="hold-transition sidebar-mini">
<div class="wrapper">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
include 'include/db.php';

// Ränge aus der Datenbank abrufen
$stmt = $conn->prepare("SELECT * FROM roles");
$stmt->execute();
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Berechtigungen aus der Datenbank abrufen
$stmtPerm = $conn->prepare("SELECT * FROM permissions");
$stmtPerm->execute();
$permissions = $stmtPerm->fetchAll(PDO::FETCH_ASSOC);
?>

<script>
$(document).ready(function () {
    // Berechtigungen dynamisch laden
    const permissions = <?= json_encode($permissions) ?>;
    const permissionsContainer = $('#permissionsContainer');

    permissions.forEach(permission => {
        const sectionLabel = permission.bereich === "1" ? 'Mitarbeiter Bereich' : 'Leitungs Bereich'; // Fix Bereich Abgleich als String
        let sectionDiv = permissionsContainer.find(`.section-${permission.bereich}`);
        if (!sectionDiv.length) {
            // Abschnitt für den Bereich erstellen, falls nicht vorhanden
            permissionsContainer.append(
                `<div class="permissions-section section-${permission.bereich}">
                    <h5>${sectionLabel}</h5>
                </div>`
            );
            sectionDiv = permissionsContainer.find(`.section-${permission.bereich}`);
        }

        // Checkbox für die Berechtigung hinzufügen
        sectionDiv.append(`
    <div class="form-check">
        <input type="checkbox" class="form-check-input" id="perm_${permission.id}" name="permissions[]" value="${permission.id}" data-name="${permission.name}">
        <label class="form-check-label" for="perm_${permission.id}">${permission.display_name} (${permission.description})</label>
    </div>
`);
    });
});
</script>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Responsive Hover Table</h3>
        <div class="card-tools">
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-add-role">Neue Rolle hinzufügen</button>
        </div>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
          <thead>
            <tr>
              <th>ID</th>
              <th>Rang</th>
              <th>Ebene</th>
              <th>Bearbeiten</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($roles as $role): ?>
              <tr>
                <td><?= htmlspecialchars($role['id']) ?></td>
                <td><?= htmlspecialchars($role['name']) ?></td>
                <td><?= htmlspecialchars($role['level']) ?></td>
                <td>
                <button type="button" class="btn btn-block btn-outline-secondary" 
        data-toggle="modal" 
        data-target="#modal-default" 
        data-id="<?= $role['id'] ?>">
    Bearbeiten
</button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
    <!-- /.card -->
  </div>
  <!-- /.col -->
</div>
<!-- /.row -->
</div>
<!-- /.container-fluid -->
</section>
    

<div class="modal fade" id="modal-add-role">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Neue Rolle erstellen</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="addRoleForm">
          <div class="card-body">
          <div class="form-group">
  <label for="roleName">Rangname</label>
  <input type="text" id="roleName" class="form-control">
</div>
<div class="form-group">
  <label for="roleLevel">Rang Ebene</label>
  <select id="roleLevel" class="custom-select">
    <option value="Inhaber">Inhaber</option>
    <option value="Geschäftsführung">Geschäftsführung</option>
    <option value="Mitarbeiter">Mitarbeiter</option>
  </select>
</div>
<div class="form-group" id="permissionsContainer">
  <!-- Dynamische Rechte erscheinen hier -->
</div>
          </div>
        </form>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
        <button type="button" class="btn btn-primary" id="saveRoleButton">Speichern</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-default">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Rolle bearbeiten</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="editRoleForm">
          <div class="card-body">
            <div class="form-group">
              <label for="roleName">Rangname</label>
              <input type="text" id="roleName" class="form-control">
            </div>
            <div class="form-group">
              <label for="roleLevel">Rang Ebene</label>
              <select id="roleLevel" class="custom-select">
                <option value="Inhaber">Inhaber</option>
                <option value="Geschäftsführung">Geschäftsführung</option>
                <option value="Mitarbeiter">Mitarbeiter</option>
              </select>
            </div>
            <div class="form-group" id="permissionsContainer">
              <!-- Dynamische Rechte erscheinen hier -->
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
        <button type="button" class="btn btn-primary" id="saveEditRoleButton">Speichern</button>
      </div>
    </div>
  </div>
</div>


<script>
$('#saveRoleButton').click(function () {
    const name = $('#roleName').val();
    const level = $('#roleLevel').val();
    const permissions = [];

    // Sammle die Namen der angekreuzten Berechtigungen
    $('#permissionsContainer input[type="checkbox"]:checked').each(function () {
        permissions.push($(this).attr('data-name')); // Verwende das Attribut 'data-name'
    });

    // AJAX-Anfrage, um die neue Rolle zu speichern
    $.ajax({
        url: 'include/add_role.php',
        type: 'POST',
        data: {
            name: name,
            level: level,
            permissions: JSON.stringify(permissions)
        },
        success: function (response) {
            console.log(response); // Debug-Ausgabe
            if (response.success) {
                alert('Rolle erfolgreich hinzugefügt.');
                location.reload();
            } else {
                alert('Fehler: ' + response.message);
            }
        },
        error: function () {
            alert('Fehler beim Speichern der Rolle.');
        }
    });
});

$('#modal-default .btn-primary').click(function () {
    const roleId = $('#modal-default').data('id'); // Hole die ID der Rolle
    const name = $('#modal-default #roleName').val();
    const level = $('#modal-default #roleLevel').val();
    const permissions = [];

    // Sammle die Berechtigungen
    $('#modal-default #permissionsContainer input[type="checkbox"]:checked').each(function () {
        permissions.push($(this).val());
    });

    console.log({ id: roleId, name, level, permissions }); // Debug-Ausgabe

    // AJAX-Anfrage senden
    $.ajax({
        url: 'include/update_role.php',
        type: 'POST',
        data: {
            id: roleId,
            name: name,
            level: level,
            permissions: JSON.stringify(permissions)
        },
        success: function (response) {
            console.log(response); // Debug-Ausgabe
            if (response.success) {
                alert('Rolle erfolgreich aktualisiert.');
                location.reload();
            } else {
                alert('Fehler: ' + response.message);
            }
        },
        error: function () {
            alert('Fehler beim Speichern der Rolle.');
        }
    });
});


$(document).on('click', '[data-target="#modal-default"]', function () {
    const roleId = $(this).data('id'); // ID der Rolle

    $.ajax({
    url: 'get_role.php',
    type: 'GET',
    data: { id: roleId },
    dataType: 'json',
    success: function (response) {
        if (response.success) {
            const role = response.role;

            // Felder mit Rollendaten füllen
            $('#modal-default #roleName').val(role.name);
            $('#modal-default #roleLevel').val(role.level);

            // Checkboxen für Permissions dynamisch erstellen
            const permissionsContainer = $('#modal-default #permissionsContainer');
            permissionsContainer.empty();

            // Hier den Code einfügen
            response.all_permissions.forEach(permission => {
                const checked = role.permissions.includes(permission.name) ? 'checked' : ''; // Vergleiche mit permission.name
                permissionsContainer.append(`
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="perm_${permission.id}" value="${permission.name}" ${checked} data-name="${permission.name}">
                        <label class="form-check-label" for="perm_${permission.id}">${permission.display_name} (${permission.description})</label>
                    </div>
                `);
            });
        } else {
            alert('Fehler: ' + response.error);
        }
    },
    error: function () {
        alert('Fehler beim Laden der Rollendaten.');
    }
});
});
</script>

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
