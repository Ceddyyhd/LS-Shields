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
    ?>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Rollenverwaltung</h3>
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
                                        data-id="<?= $role['id'] ?>" 
                                        data-name="<?= htmlspecialchars($role['name']) ?>">
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
</div>

<!-- Modal für neue Rolle -->
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
                    <div class="form-group">
                        <label for="roleName">Rangname</label>
                        <input type="text" id="addRoleName" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="roleLevel">Rang Ebene</label>
                        <select id="addRoleLevel" class="custom-select">
                            <option value="Inhaber">Inhaber</option>
                            <option value="Geschäftsführung">Geschäftsführung</option>
                            <option value="Mitarbeiter">Mitarbeiter</option>
                        </select>
                    </div>
                    <div class="form-group" id="addPermissionsContainer">
                        <!-- Dynamische Rechte erscheinen hier -->
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
                <button type="button" class="btn btn-primary" id="saveAddRoleButton">Speichern</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal für Bearbeiten -->
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
                    <div class="form-group">
                        <label for="editRoleName">Rangname</label>
                        <input type="text" id="editRoleName" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="editRoleLevel">Rang Ebene</label>
                        <select id="editRoleLevel" class="custom-select">
                            <option value="Inhaber">Inhaber</option>
                            <option value="Geschäftsführung">Geschäftsführung</option>
                            <option value="Mitarbeiter">Mitarbeiter</option>
                        </select>
                    </div>
                    <div class="form-group" id="editPermissionsContainer">
                        <!-- Dynamische Rechte erscheinen hier -->
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
                <button type="button" class="btn btn-primary" id="saveEditRoleButton">Speichern</button>
            </div>
        </div>
    </div>
</div>

<script>
// Rechte dynamisch laden für "Neue Rolle hinzufügen"
$('#modal-add-role').on('show.bs.modal', function () {
    const permissionsContainer = $('#addPermissionsContainer');
    permissionsContainer.empty();

    $.ajax({
        url: 'include/get_permissions.php',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                response.permissions.forEach(permission => {
                    permissionsContainer.append(`
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="${permission.name}">
                            <label class="form-check-label" for="${permission.name}">${permission.description}</label>
                        </div>
                    `);
                });
            }
        },
        error: function () {
            alert('Fehler beim Laden der Rechte.');
        }
    });
});

// Rechte und Rollendaten laden für "Bearbeiten"
$(document).on('click', '[data-target="#modal-default"]', function () {
    const roleId = $(this).data('id');
    const permissionsContainer = $('#editPermissionsContainer');
    permissionsContainer.empty();

    $.ajax({
        url: 'include/get_permissions.php',
        type: 'GET',
        dataType: 'json',
        success: function (permissionsResponse) {
            if (permissionsResponse.success) {
                $.ajax({
                    url: 'include/get_role.php',
                    type: 'GET',
                    data: { id: roleId },
                    dataType: 'json',
                    success: function (roleResponse) {
                        if (roleResponse.success) {
                            $('#editRoleName').val(roleResponse.role.name);
                            $('#editRoleLevel').val(roleResponse.role.level);

                            permissionsResponse.permissions.forEach(permission => {
                                const checked = roleResponse.role.permissions[permission.name] ? 'checked' : '';
                                permissionsContainer.append(`
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="${permission.name}" ${checked}>
                                        <label class="form-check-label" for="${permission.name}">${permission.description}</label>
                                    </div>
                                `);
                            });
                        }
                    }
                });
            }
        }
    });
});

// Speichern von "Neue Rolle"
$('#saveAddRoleButton').click(function () {
    const name = $('#addRoleName').val();
    const level = $('#addRoleLevel').val();
    const permissions = {};

    $('#addPermissionsContainer input[type="checkbox"]').each(function () {
        permissions[$(this).attr('id')] = $(this).is(':checked');
    });

    $.ajax({
        url: 'include/add_role.php',
        type: 'POST',
        data: {
            name: name,
            level: level,
            permissions: JSON.stringify(permissions)
        },
        success: function (response) {
            if (response.success) {
                alert('Neue Rolle erfolgreich hinzugefügt.');
                location.reload();
            } else {
                alert('Fehler: ' + response.message);
            }
        },
        error: function () {
            alert('Fehler beim Hinzufügen der Rolle.');
        }
    });
});

// Speichern von "Bearbeiten"
$('#saveEditRoleButton').click(function () {
    const roleId = $('#editRoleName').data('role-id');
    const name = $('#editRoleName').val();
    const level = $('#editRoleLevel').val();
    const permissions = {};

    $('#editPermissionsContainer input[type="checkbox"]').each(function () {
        permissions[$(this).attr('id')] = $(this).is(':checked');
    });

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
            if (response.success) {
                alert('Änderungen erfolgreich gespeichert.');
                location.reload();
            } else {
                alert('Fehler: ' + response.message);
            }
        },
        error: function () {
            alert('Fehler beim Speichern der Änderungen.');
        }
    });
});
</script>

</body>
</html>
