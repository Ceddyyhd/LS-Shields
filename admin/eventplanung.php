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
// SQL-Abfrage zum Abrufen aller Events aus der eventplanung-Tabelle
$query = "SELECT eventplanung.*, 
                 users.name AS event_lead_name, 
                 users.profile_image AS event_lead_profile_image,
                 IFNULL(GROUP_CONCAT(DISTINCT team_users.name ORDER BY team_users.name), '') AS team_members_names,
                 IFNULL(GROUP_CONCAT(DISTINCT team_users.profile_image ORDER BY team_users.name), '') AS team_members_images
          FROM eventplanung
          LEFT JOIN users ON eventplanung.event_lead = users.id
          LEFT JOIN event_mitarbeiter_anmeldung ON eventplanung.id = event_mitarbeiter_anmeldung.event_id
          LEFT JOIN users AS team_users ON event_mitarbeiter_anmeldung.employee_id = team_users.id
          WHERE eventplanung.id = :event_id
          GROUP BY eventplanung.id";

$stmt = $conn->prepare($query);
$stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
$stmt->execute();
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    die('Eventplanung nicht gefunden.');
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
                    <th style="width: 20%">Project Name</th>
                    <th style="width: 30%">Team Members</th>
                    <th>Status</th>
                    <th style="width: 20%">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $event): ?>
                    <tr>
                        <td>#</td>
                        <td>
                            <a><?= htmlspecialchars($event['vorname_nachname']); ?></a>
                            <br/>
                            <small>Created <?= date('d.m.Y', strtotime($event['datum_uhrzeit'])); ?></small>
                        </td>
                        <td>
                        <ul class="list-inline">
        <?php
        // Teammitglieder und deren Profilbilder anzeigen
        $team_members = explode(',', $event['team_members_names']);
        $team_member_images = explode(',', $event['team_members_images']);
        foreach ($team_members as $key => $member) {
            // Tooltip mit dem Namen des Mitarbeiters
            echo "<li class='list-inline-item' data-toggle='tooltip' title='" . htmlspecialchars($member) . "'>";
            echo "<img alt='Avatar' class='table-avatar' src='" . htmlspecialchars($team_member_images[$key]) . "'>";
            echo "</li>";
        }
        ?>
    </ul>
    <script>
        $(document).ready(function () {
    // Initialisiere alle Tooltips auf der Seite
    $('[data-toggle="tooltip"]').tooltip();
});
    </script>
                        </td>
                        <td class="project-state">
                            <?php
                            // Status-Badge basierend auf dem Status
                            $status = htmlspecialchars($event['status']);
                            if ($status == 'in Planung') {
                                echo "<span class='badge badge-warning'>In Planung</span>";
                            } elseif ($status == 'in Durchführung') {
                                echo "<span class='badge badge-danger'>In Durchführung</span>";
                            } elseif ($status == 'Abgeschlossen') {
                                echo "<span class='badge badge-success'>Abgeschlossen</span>";
                            }
                            ?>
                        </td>
                        <td class="project-actions text-right">
                            <!-- View Button, der zu einer Detailseite weiterleitet -->
                            <a class="btn btn-primary btn-sm" href="eventplanung_akte.php?id=<?= $event['id']; ?>">
                                <i class="fas fa-folder"></i> View
                            </a>
                            <a class="btn btn-info btn-sm" href="#">
                                <i class="fas fa-pencil-alt"></i> Edit
                            </a>
                            <a class="btn btn-danger btn-sm" href="#">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
    
    
    

      


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
