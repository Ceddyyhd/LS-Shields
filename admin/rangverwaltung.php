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
    $stmt = $conn->prepare("SELECT * FROM roles ORDER BY value DESC");
    $stmt->execute();
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmtArea = $conn->prepare("SELECT * FROM permissions_areas");
    $stmtArea->execute();
    $areas = $stmtArea->fetchAll(PDO::FETCH_ASSOC);

    // Berechtigungen abrufen und mit Bereichsdaten zusammenführen
    $stmtPerm = $conn->prepare("
        SELECT p.*, pa.display_name AS bereich_display_name
        FROM permissions p
        LEFT JOIN permissions_areas pa ON p.bereich = pa.id
    ");
    $stmtPerm->execute();
    $permissions = $stmtPerm->fetchAll(PDO::FETCH_ASSOC);

    // Die Daten an JavaScript übergeben
    echo '<script>';
    echo 'const permissions = ' . json_encode($permissions) . ';';
    echo 'const areas = ' . json_encode($areas) . ';';
    echo '</script>';
    ?>


<script>
    $(document).ready(function () {
        // Bereichsdaten und Berechtigungen dynamisch laden
        const permissions = <?= json_encode($permissions) ?>;
        const areas = <?= json_encode($areas) ?>;

        const permissionsContainer = $('#permissionsContainer');

        // Bereichsdaten in ein Map umwandeln, um den Namen schnell zu finden
        const areaMap = {};
        areas.forEach(area => {
            areaMap[area.id] = area.display_name;
        });

        // Alle Berechtigungen gruppiert nach Bereich
        const permissionsByArea = {};
        permissions.forEach(permission => {
            if (!permissionsByArea[permission.bereich]) {
                permissionsByArea[permission.bereich] = [];
            }
            permissionsByArea[permission.bereich].push(permission);
        });

        // Dynamisch HTML für jedes Bereich erstellen
        areas.forEach(area => {
            const sectionLabel = areaMap[area.id] || 'Unbekannter Bereich';

            let sectionDiv = permissionsContainer.find(`.section-${area.id}`);
            if (!sectionDiv.length) {
                // Abschnitt für den Bereich erstellen, falls nicht vorhanden
                permissionsContainer.append(
                    `<div class="permissions-section section-${area.id}">
                        <h5 data-widget="expandable-table" aria-expanded="false" class="expandable-table">
                            <i class="expandable-table-caret fas fa-caret-right fa-fw"></i>
                            ${sectionLabel}
                        </h5>
                        <div class="expandable-body" style="display: none;">
                            <table class="table table-hover">
                                <tbody class="permissions-list">
                                </tbody>
                            </table>
                        </div>
                    </div>`
                );
                sectionDiv = permissionsContainer.find(`.section-${area.id}`);
            }

            // Berechtigungen für den Bereich hinzufügen
            const permissionList = permissionsByArea[area.id];
            const permissionsListContainer = sectionDiv.find('.permissions-list');
            permissionList.forEach(permission => {
                permissionsListContainer.append(`
                    <tr>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="perm_${permission.id}" name="permissions[]" value="${permission.id}">
                                <label class="form-check-label" for="perm_${permission.id}">
                                    ${permission.display_name} (${permission.description})
                                </label>
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


<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Expandable Table Tree</h3>
      </div>
      <!-- ./card-header -->
      <div class="card-body p-0">
        <table class="table table-hover">
          <tbody>
            <tr data-widget="expandable-table" aria-expanded="true">
              <td>
                <i class="expandable-table-caret fas fa-caret-right fa-fw"></i>
                Mitarbeiter Bereich
              </td>
            </tr>
            <tr class="expandable-body">
              <td>
                <div class="p-0">
                  <table class="table table-hover">
                    <tbody>
                      <tr data-widget="expandable-table" aria-expanded="false">
                        <td>
                          <i class="expandable-table-caret fas fa-caret-right fa-fw"></i>
                          Dashboard
                        </td>
                      </tr>
                      <tr class="expandable-body">
                        <td>
                          <div class="p-0">
                            <table class="table table-hover">
                              <tbody>
                                <tr>
                                  <div class="form-check">
                                    <input class="form-check-input" type="checkbox">
                                    <label class="form-check-label">Checkbox</label>
                                  </div>
                                </tr>
                                <tr>
                                  <div class="form-check">
                                    <input class="form-check-input" type="checkbox">
                                    <label class="form-check-label">Checkbox</label>
                                  </div>
                                </tr>
                                <tr>
                                  <div class="form-check">
                                    <input class="form-check-input" type="checkbox">
                                    <label class="form-check-label">Checkbox</label>
                                  </div>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
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
    

<!-- Modal-Code und Skripte hier -->

<script>
    // Weitere Modal-Funktionen bleiben hier gleich...
</script>

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
