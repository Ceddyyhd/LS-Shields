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

// Berechtigungen abrufen und mit Bereichsdaten zusammenführen
$stmtArea = $conn->prepare("SELECT * FROM permissions_areas");
$stmtArea->execute();
$areas = $stmtArea->fetchAll(PDO::FETCH_ASSOC);

// Berechtigungen abrufen
$stmtPerm = $conn->prepare("SELECT p.*, pa.display_name AS bereich_display_name FROM permissions p LEFT JOIN permissions_areas pa ON p.bereich = pa.id");
$stmtPerm->execute();
$permissions = $stmtPerm->fetchAll(PDO::FETCH_ASSOC);

// Debug: Überprüfen, ob $areas leer ist
if (empty($areas)) {
    echo "Keine Bereiche in der Datenbank gefunden.";
} else {
    // Bereichsdaten nach parent_id gruppieren
    $areaMap = [];
    // Bereichsdaten nach parent_id gruppieren
$groupedAreas = [];

// Iteriere durch alle Bereiche und gruppiere sie nach ihrer parent_id
foreach ($areas as $area) {
    // Hauptbereich
    if ($area['parent_id'] === NULL) {
        $groupedAreas[$area['id']] = [
            'area' => $area,
            'children' => []
        ];
    } else {
        // Unterbereiche der entsprechenden Parent-ID zuweisen
        if (!isset($groupedAreas[$area['parent_id']])) {
            $groupedAreas[$area['parent_id']] = [
                'area' => null,
                'children' => []
            ];
        }
        $groupedAreas[$area['parent_id']]['children'][] = $area;
    }
}

    // Daten für JavaScript vorbereiten
    echo '<pre>';
print_r($areas); // Zeigt alle Bereiche an
echo '</pre>';

echo '<pre>';
print_r($permissions); // Zeigt alle Berechtigungen an
echo '</pre>';
}
?>





<script>
$(document).ready(function() {
    const permissions = <?= json_encode($permissions) ?>;
    const areas = <?= json_encode(array_values($groupedAreas)) ?>; // Sicherstellen, dass es ein Array ist

    const permissionsContainer = $('#permissionsContainer');

    areas.forEach(areaGroup => {
        const parentArea = areaGroup.area; // Der Hauptbereich
        const children = areaGroup.children; // Unterbereiche

        const sectionLabel = parentArea.display_name || 'Unbekannter Bereich';

        let sectionDiv = permissionsContainer.find(`.section-${parentArea.id}`);
        if (!sectionDiv.length) {
            permissionsContainer.append(`
                <div class="permissions-section section-${parentArea.id}">
                    <h5 class="expandable-table" data-widget="expandable-table" aria-expanded="false">
                        <i class="expandable-table-caret fas fa-caret-right fa-fw"></i>
                        ${sectionLabel}
                    </h5>
                    <div class="expandable-body" style="display: none;">
                        <table class="table table-hover">
                            <tbody class="permissions-list-${parentArea.id}">
                            </tbody>
                        </table>
                    </div>
                </div>
            `);
            sectionDiv = permissionsContainer.find(`.section-${parentArea.id}`);
        }

        // Unterbereiche hinzufügen (z.B. Dashboard, Eventakte)
        children.forEach(child => {
            const permissionsListContainer = sectionDiv.find(`.permissions-list-${parentArea.id}`);

            permissionsListContainer.append(`
                <tr data-widget="expandable-table" aria-expanded="false">
                    <td>
                        <i class="expandable-table-caret fas fa-caret-right fa-fw"></i>
                        ${child.display_name}
                    </td>
                </tr>
                <tr class="expandable-body">
                    <td>
                        <div class="p-0">
                            <table class="table table-hover">
                                <tbody>
                                    <tr>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="perm_${child.id}" name="permissions[]" value="${child.id}" data-name="${child.name}">
                                            <label class="form-check-label" for="perm_${child.id}">${child.display_name} (${child.description})</label>
                                        </div>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
            `);
        });

        // Click Event für das Klappen des Bereichs
        sectionDiv.find('h5').on('click', function() {
            const expandableBody = $(this).next('.expandable-body');
            expandableBody.toggle(); // Zeigt oder versteckt das Dropdown
            const caret = $(this).find('.expandable-table-caret');
            caret.toggleClass('fa-caret-right fa-caret-down'); // Dreht das Caret-Symbol
        });
    });
});






</script>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Responsive Hover Table</h3>
        <div class="card-tools">
        <?php if ($_SESSION['permissions']['role_create'] ?? false): ?>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-add-role">Neue Rolle hinzufügen</button>
          <?php endif; ?>
        </div>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
          <thead>
            <tr>
              <th>ID</th>
              <th>Rang</th>
              <th>Ebene</th>
              <th>value</th>
              <th>Bearbeiten</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($roles as $role): ?>
            <tr>
              <td><?= htmlspecialchars($role['id']) ?></td>
              <td><?= htmlspecialchars($role['name']) ?></td>
              <td><?= htmlspecialchars($role['level']) ?></td>
              <td><?= htmlspecialchars($role['value']) ?></td>
              <td>
                <?php if ((int)$role['id'] !== 1 && ($_SESSION['permissions']['role_change'] ?? false)): ?>
                  <button type="button" class="btn btn-block btn-outline-secondary" 
                          data-toggle="modal" 
                          data-target="#modal-default" 
                          data-id="<?= $role['id'] ?>">
                      Bearbeiten
                  </button>
                <?php endif; ?>
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
    

<!-- Modal für das Erstellen einer Rolle -->
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
            <div class="form-group">
              <label for="roleValue">Wert (Value)</label>
              <input type="number" id="roleValue" class="form-control" min="1" max="100" placeholder="Zahlenwert für den Rang">
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

<!-- Modal für das Bearbeiten einer Rolle -->
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
            <div class="form-group">
              <label for="roleValue">Wert (Value)</label>
              <input type="number" id="roleValue" class="form-control" min="1" max="100" placeholder="Zahlenwert für den Rang">
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
    // Funktion zum Erstellen einer Rolle
    $('#saveRoleButton').click(function () {
        const name = $('#roleName').val();
        const level = $('#roleLevel').val();
        const value = $('#roleValue').val(); // Neuen Wert holen
        const permissions = [];

        $('#permissionsContainer input[type="checkbox"]:checked').each(function () {
            permissions.push($(this).attr('data-name'));
        });

        $.ajax({
            url: 'include/add_role.php',
            type: 'POST',
            data: {
                name: name,
                level: level,
                value: value, // Value mit senden
                permissions: JSON.stringify(permissions)
            },
            success: function (response) {
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

    // Funktion zum Bearbeiten einer Rolle
    $('#modal-default .btn-primary').click(function () {
        const roleId = $('#modal-default').data('id');
        const name = $('#modal-default #roleName').val();
        const level = $('#modal-default #roleLevel').val();
        const value = $('#modal-default #roleValue').val(); // Neuen Wert holen
        const permissions = [];

        $('#modal-default #permissionsContainer input[type="checkbox"]:checked').each(function () {
            permissions.push($(this).val());
        });

        $.ajax({
            url: 'include/update_role.php',
            type: 'POST',
            data: {
                id: roleId,
                name: name,
                level: level,
                value: value, // Value mit senden
                permissions: JSON.stringify(permissions)
            },
            success: function (response) {
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
    const roleId = $(this).data('id'); // ID der Rolle aus Button
    $('#modal-default').data('id', roleId); // ID an Modal binden

    $.ajax({
        url: 'include/get_role.php',
        type: 'GET',
        data: { id: roleId },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                const role = response.role;

                // Felder mit Rollendaten füllen
                $('#modal-default #roleName').val(role.name);
                $('#modal-default #roleLevel').val(role.level);
                $('#modal-default #roleValue').val(role.value); // Wert eintragen

                // Bereichsdaten für den Bereichsnamen
                const areaMap = {};
                response.areas.forEach(area => {
                    areaMap[area.id] = area.display_name;
                });

                // Checkboxen für Permissions dynamisch erstellen
                const permissionsContainer = $('#modal-default #permissionsContainer');
                permissionsContainer.empty();

                response.all_permissions.forEach(permission => {
                    const sectionLabel = areaMap[permission.bereich] || 'Unbekannter Bereich'; // Bereich dynamisch bestimmen

                    // Überprüfen, ob der Abschnitt schon existiert
                    let sectionDiv = permissionsContainer.find(`.section-${permission.bereich}`);
                    if (!sectionDiv.length) {
                        // Abschnitt erstellen, falls er nicht existiert
                        permissionsContainer.append(`
                            <div class="permissions-section section-${permission.bereich}">
                                <h5>${sectionLabel}</h5>
                            </div>
                        `);
                        sectionDiv = permissionsContainer.find(`.section-${permission.bereich}`);
                    }

                    // Checkbox hinzufügen
                    const checked = role.permissions.includes(permission.name) ? 'checked' : ''; // Überprüfung mit permission.name
                    sectionDiv.append(`
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
