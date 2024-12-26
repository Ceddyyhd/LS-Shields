<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<?php include 'include/header.php'; ?>

<body class="hold-transition sidebar-mini">
<div class="wrapper">

<?php include 'include/navbar.php'; ?>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>


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
    <?php
// SQL-Abfrage zum Abrufen aller Events ohne Duplikate und NULL-Werte
$query = "
    SELECT DISTINCT eventplanung.id, 
           eventplanung.event, 
           eventplanung.anmerkung, 
           eventplanung.status, 
           eventplanung.vorname_nachname, 
           eventplanung.datum_uhrzeit
    FROM eventplanung
    LEFT JOIN event_mitarbeiter_anmeldung ON eventplanung.id = event_mitarbeiter_anmeldung.event_id
    LEFT JOIN users ON eventplanung.event_lead = users.id";

$stmt = $conn->prepare($query);
$stmt->execute();

// Alle Events abrufen
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Debugging: Alle abgerufenen Events ausgeben
echo '<pre>';
print_r($events); // Gibt alle abgerufenen Events aus
echo '</pre>';
?>
<?php
foreach ($events as &$event) {
    // Teammitglieder für jedes Event abfragen und doppelte IDs vermeiden
    $teamQuery = "SELECT DISTINCT users.name, users.profile_image 
                  FROM event_mitarbeiter_anmeldung
                  LEFT JOIN users ON event_mitarbeiter_anmeldung.employee_id = users.id
                  WHERE event_mitarbeiter_anmeldung.event_id = :event_id";

    $teamStmt = $conn->prepare($teamQuery);
    $teamStmt->bindParam(':event_id', $event['id'], PDO::PARAM_INT);
    $teamStmt->execute();
    $team_members = $teamStmt->fetchAll(PDO::FETCH_ASSOC);

    // Teammitglieder in das Event-Datenfeld einfügen
    $event['team_members'] = $team_members;
}
?>
    <!-- Main content -->
    
    <div class="card">
    <div class="card-header">
        <h3 class="card-title">Projects</h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped projects">
            <thead>
                <tr>
                    <th style="width: 1%">#</th>
                    <th style="width: 20%">Ansprechpartner</th>
                    <th style="width: 20%">Event</th> <!-- Neue Spalte für Event -->
                    <th style="width: 20%">Anmerkung</th> <!-- Neue Spalte für Anmerkung -->
                    <th style="width: 30%">Team Members</th>
                    <th>Status</th>
                    <th style="width: 20%">Actions</th>
                </tr>
            </thead>
            <tbody>
    <?php
    // Debugging: Zeige die Events an
    print_r($events);
    foreach ($events as $event): 
    ?>
        <tr>
            <td><?= htmlspecialchars($event['id']); ?></td>
            <td><?= htmlspecialchars($event['vorname_nachname']); ?></td>
            <td><?= htmlspecialchars($event['event']); ?></td>
            <td><?= htmlspecialchars($event['anmerkung']); ?></td>
            <td><?= htmlspecialchars($event['status']); ?></td>
            <td class="project-actions text-right">
                <a class="btn btn-primary btn-sm" href="eventplanung_akte.php?id=<?= $event['id']; ?>">View</a>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>

        </table>
    </div>
</div>

<!-- JavaScript zum Initialisieren der Tooltips -->
<script>
    $(document).ready(function () {
    // Initialisiere alle Tooltips auf der Seite
    $('[data-toggle="tooltip"]').tooltip();
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
