<!DOCTYPE html>
<html lang="en">
<?php include 'include/header.php'; ?>

<body class="hold-transition sidebar-mini">
<div class="wrapper">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php include 'include/navbar.php'; ?>

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
          <h3 class="card-title">Rollenübersicht</h3>
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
</div>

<!-- Modal: Neue Rolle hinzufügen -->
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
            <label for="addRoleName">Rangname</label>
            <input type="text" id="addRoleName" class="form-control">
          </div>
          <div class="form-group">
            <label for="addRoleLevel">Rang Ebene</label>
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

<!-- Modal: Rolle bearbeiten -->
<div class="modal fade" id="modal-default">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Rang bearbeiten</h4>
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
        <button type="button" class="btn btn-primary" id="saveEditRoleButton">Änderungen speichern</button>
      </div>
    </div>
  </div>
</div>

<script>
  // Modal "Rolle bearbeiten" laden
  $(document).on('click', '[data-target="#modal-default"]', function () {
    const roleId = $(this).data('id');
    $.ajax({
      url: 'include/get_role.php',
      type: 'GET',
      data: { id: roleId },
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          $('#editRoleName').val(response.role.name);
          $('#editRoleLevel').val(response.role.level);

          const permissionsContainer = $('#editPermissionsContainer');
          permissionsContainer.empty();
          const permissions = response.role.permissions;
          for (const [key, value] of Object.entries(permissions)) {
            permissionsContainer.append(`
              <div class="form-check">
                <input type="checkbox" class="form-check-input" id="${key}" ${value ? 'checked' : ''}>
                <label class="form-check-label" for="${key}">${key}</label>
              </div>
            `);
          }
        }
      },
      error: function () {
        alert('Fehler beim Laden der Rolle.');
      }
    });
  });

  // Änderungen speichern
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
        permissions: JSON.stringify(permissions),
      },
      success: function (response) {
        console.log('Antwort vom Server:', response);
        if (response.success) {
          alert('Änderungen erfolgreich gespeichert.');
          location.reload();
        } else {
          alert('Fehler: ' + response.message);
        }
      },
      error: function (xhr, status, error) {
        console.error('AJAX-Fehler:', error);
        alert('Fehler beim Speichern der Änderungen.');
      },
    });
  });
  // Rolle hinzufügen
$('#saveAddRoleButton').click(function () {
    const name = $('#addRoleName').val();
    const level = $('#addRoleLevel').val();

    // Validierung
    if (!name || !level) {
        alert('Bitte alle Felder ausfüllen.');
        return;
    }

    // Leere Berechtigungen initialisieren
    const permissions = {};
    $('#addPermissionsContainer input[type="checkbox"]').each(function () {
        const key = $(this).attr('id');
        const value = $(this).is(':checked');
        permissions[key] = value;
    });

    // AJAX-Anfrage senden
    $.ajax({
        url: 'include/add_role.php',
        type: 'POST',
        data: {
            name: name,
            level: level,
            permissions: JSON.stringify(permissions),
        },
        success: function (response) {
            console.log('Antwort vom Server:', response); // Debugging
            if (response.success) {
                alert('Neue Rolle erfolgreich hinzugefügt.');
                location.reload(); // Seite aktualisieren
            } else {
                alert('Fehler: ' + response.message);
            }
        },
        error: function (xhr, status, error) {
            console.error('Fehler beim Hinzufügen:', error); // Debugging
            alert('Fehler beim Hinzufügen der Rolle.');
        },
    });
});
</script>

<!-- Main Footer -->
<footer class="main-footer">
  <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong>
</footer>
</div>
</body>
</html>
