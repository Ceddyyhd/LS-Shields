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
              <input type="text" id="roleName" class="form-control" placeholder="Rangname" required>
            </div>
            <div class="form-group">
              <label for="roleLevel">Ebene</label>
              <select id="roleLevel" class="custom-select form-control-border" required>
                <option value="Inhaber">Inhaber</option>
                <option value="Geschäftsführung">Geschäftsführung</option>
                <option value="Ausbildung">Ausbildung</option>
                <option value="Mitarbeiter">Mitarbeiter</option>
              </select>
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

<script>
  $('#saveRoleButton').click(function () {
    const name = $('#roleName').val();
    const level = $('#roleLevel').val();

    $.ajax({
        url: 'include/add_role.php',
        type: 'POST',
        data: { name: name, level: level },
        success: function (response) {
            if (response.success) {
                alert('Rolle erfolgreich hinzugefügt.');
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

</script>

    
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
              
            <form>
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Rang</label>
                    <input class="form-control" type="Rang" placeholder="CEO">                  
                  </div>
                <div class="form-group">
                  <label for="exampleSelectBorder">Rang Ebene </label>
                  <select class="custom-select form-control-border" id="exampleSelectBorder">
                    <option>Inhaber</option>
                    <option>Geschäftsführung</option>
                    <option>Ausbildung</option>
                    <option>Mitarbeiter</option>
                  </select>
                </div>
                <label for="exampleInputEmail1">Mitarbeiter Bereich</label>
                  <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="exampleCheck1">
                    <label class="form-check-label" for="exampleCheck1">Recht 1</label>
                    <div>
                    <input type="checkbox" class="form-check-input" id="exampleCheck2">
                    <label class="form-check-label" for="exampleCheck2">Recht 2</label>
                    </div>
                  </div>
                  <label for="exampleInputEmail1">Leitungs Bereich</label>
                  <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="exampleCheck1">
                    <label class="form-check-label" for="exampleCheck1">Recht 1</label>
                    <div>
                    <input type="checkbox" class="form-check-input" id="exampleCheck2">
                    <label class="form-check-label" for="exampleCheck2">Recht 2</label>
                    </div>
                  </div>
                </div>
                <!-- /.card-body -->
              </form>


            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary">Save changes</button>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>

      

    <script>$(document).on('click', '[data-target="#modal-default"]', function () {
    const roleId = $(this).data('id');
    const roleName = $(this).data('name'); // Rangname aus dem Button

    // Rangname im Modal-Titel setzen
    $('#modalRoleName').text(roleName);

    // AJAX-Anfrage, um die Rang-Daten zu laden (siehe vorherige Schritte)

    // AJAX-Anfrage, um die Rang-Daten abzurufen
    $.ajax({
        url: 'include/get_role.php', // PHP-Datei, die die Rangdaten liefert
        type: 'GET',
        data: { id: roleId },
        dataType: 'json',
        success: function (response) {
            // Fülle die Modal-Felder mit den Daten
            $('#modal-default input[placeholder="CEO"]').val(response.name);
            $('#modal-default select#exampleSelectBorder').val(response.level);
            
            // Rechte markieren
            $('#modal-default input[type="checkbox"]').each(function () {
                const checkbox = $(this);
                const right = checkbox.attr('id'); // Checkbox-ID entspricht dem Recht
                checkbox.prop('checked', response.permissions.includes(right));
            });
        },
        error: function () {
            alert('Fehler beim Laden der Rangdaten.');
        }
    });
});
</script>
<script> 
  $('#modal-default .btn-primary').click(function () {
    const roleId = $('[data-target="#modal-default"]').data('id'); // ID des Rangs
    const name = $('#modal-default input[placeholder="CEO"]').val();
    const level = $('#modal-default select#exampleSelectBorder').val();
    const permissions = [];

    // Alle markierten Rechte sammeln
    $('#modal-default input[type="checkbox"]:checked').each(function () {
        permissions.push($(this).attr('id')); // ID entspricht dem Recht
    });

    // AJAX-Anfrage, um die Änderungen zu speichern
    $.ajax({
        url: 'include/update_role.php', // PHP-Datei, die die Änderungen speichert
        type: 'POST',
        data: { id: roleId, name: name, level: level, permissions: JSON.stringify(permissions) },
        success: function (response) {
            if (response.success) {
                alert('Rang erfolgreich aktualisiert.');
                location.reload(); // Seite neu laden, um Änderungen anzuzeigen
            } else {
                alert('Fehler beim Speichern: ' + response.message);
            }
        },
        error: function () {
            alert('Fehler beim Speichern der Änderungen.');
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
