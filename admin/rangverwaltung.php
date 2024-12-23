<!DOCTYPE html>
<html lang="en">
<?php include 'include/header.php'; ?>

<body class="hold-transition sidebar-mini">
<div class="wrapper">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php include 'include/navbar.php'; ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Rangverwaltung</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Rangverwaltung</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Rollen</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-add-role">Neue Rolle hinzufügen</button>
            </div>
          </div>
          <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Ebene</th>
                  <th>Bearbeiten</th>
                </tr>
              </thead>
              <tbody id="roles-table-body">
                <!-- Dynamische Rollen werden hier geladen -->
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

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
        <form id="add-role-form">
          <div class="form-group">
            <label for="roleName">Rollenname</label>
            <input type="text" id="roleName" class="form-control" placeholder="Rollenname">
          </div>
          <div class="form-group">
            <label for="roleLevel">Ebene</label>
            <input type="text" id="roleLevel" class="form-control" placeholder="Ebene">
          </div>
          <div class="form-group" id="permissions-container">
            <!-- Dynamische Berechtigungen werden hier eingefügt -->
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
        <button type="button" class="btn btn-primary" id="save-role-button">Speichern</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-edit-role">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Rolle bearbeiten</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="edit-role-form">
          <div class="form-group">
            <label for="editRoleName">Rollenname</label>
            <input type="text" id="editRoleName" class="form-control">
          </div>
          <div class="form-group">
            <label for="editRoleLevel">Ebene</label>
            <input type="text" id="editRoleLevel" class="form-control">
          </div>
          <div class="form-group" id="edit-permissions-container">
            <!-- Dynamische Berechtigungen werden hier eingefügt -->
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
        <button type="button" class="btn btn-primary" id="update-role-button">Speichern</button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function () {
  // Rollen laden
  function loadRoles() {
    $.get('include/get_role.php', function (response) {
      if (response.success) {
        const roles = response.roles;
        const tableBody = $('#roles-table-body');
        tableBody.empty();

        roles.forEach(role => {
          tableBody.append(`
            <tr>
              <td>${role.id}</td>
              <td>${role.name}</td>
              <td>${role.level}</td>
              <td>
                <button class="btn btn-secondary edit-role" data-id="${role.id}">Bearbeiten</button>
              </td>
            </tr>
          `);
        });
      }
    });
  }

  // Berechtigungen laden
  function loadPermissions(container, selectedPermissions = []) {
    $.get('include/get_permissions.php', function (response) {
      if (response.success) {
        const permissions = response.permissions;
        container.empty();

        permissions.forEach(permission => {
          const isChecked = selectedPermissions.includes(permission.id.toString()) ? 'checked' : '';
          container.append(`
            <div class="form-check">
              <input type="checkbox" class="form-check-input" id="permission-${permission.id}" value="${permission.id}" ${isChecked}>
              <label class="form-check-label" for="permission-${permission.id}">${permission.name}</label>
            </div>
          `);
        });
      }
    });
  }

  // Neue Rolle speichern
  $('#save-role-button').click(function () {
    const name = $('#roleName').val();
    const level = $('#roleLevel').val();
    const permissions = [];

    $('#permissions-container input:checked').each(function () {
      permissions.push($(this).val());
    });

    $.post('include/add_role.php', {
      name,
      level,
      permissions: JSON.stringify(permissions)
    }, function (response) {
      if (response.success) {
        $('#modal-add-role').modal('hide');
        loadRoles();
      } else {
        alert('Fehler: ' + response.message);
      }
    });
  });

  // Rolle bearbeiten
  $(document).on('click', '.edit-role', function () {
    const roleId = $(this).data('id');

    $.get('include/get_role.php?id=' + roleId, function (response) {
      if (response.success) {
        const role = response.role;
        $('#editRoleName').val(role.name);
        $('#editRoleLevel').val(role.level);
        loadPermissions($('#edit-permissions-container'), role.permissions);
        $('#update-role-button').data('id', roleId);
        $('#modal-edit-role').modal('show');
      }
    });
  });

  // Aktualisierte Rolle speichern
  $('#update-role-button').click(function () {
    const roleId = $(this).data('id');
    const name = $('#editRoleName').val();
    const level = $('#editRoleLevel').val();
    const permissions = [];

    $('#edit-permissions-container input:checked').each(function () {
      permissions.push($(this).val());
    });

    $.post('include/update_role.php', {
      id: roleId,
      name,
      level,
      permissions: JSON.stringify(permissions)
    }, function (response) {
      if (response.success) {
        $('#modal-edit-role').modal('hide');
        loadRoles();
      } else {
        alert('Fehler: ' + response.message);
      }
    });
  });

  loadRoles();
});
</script>

</body>
</html>
