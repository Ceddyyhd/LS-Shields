<!DOCTYPE html>
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
  <!-- /.row -->

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
              <input type="text" id="addRoleName" class="form-control" required>
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
          <h4 class="modal-title">Rang bearbeiten: <span id="modalRoleName"></span></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="editRoleForm">
            <div class="form-group">
              <label for="editRoleName">Rangname</label>
              <input type="text" id="editRoleName" class="form-control" required>
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
</div>

<script>
  // Add Role Script
  $('#saveAddRoleButton').click(function () {
    const name = $('#addRoleName').val();
    const level = $('#addRoleLevel').val();

    // Implement AJAX logic for adding roles
  });

  // Edit Role Script
  $(document).on('click', '[data-target="#modal-default"]', function () {
    const roleId = $(this).data('id');
    // Implement AJAX logic for loading and saving roles
  });
</script>
</body>
</html>
