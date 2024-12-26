<!DOCTYPE html>
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
                    <h1 class="m-0">Events</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Events</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <?php
$query = "
SELECT id, event, anmerkung, status, vorname_nachname, datum_uhrzeit
FROM eventplanung
ORDER BY datum_uhrzeit DESC"; 

$stmt = $conn->prepare($query);
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($events as &$event) {
echo "<pre>Event vor Team-Mitgliedern: ";
print_r($event); // Gibt das Event ohne Team-Mitglieder aus
echo "</pre>";

// Team-Mitglieder abfragen


$teamStmt = $conn->prepare($teamQuery);
$teamStmt->bindParam(':event_id', $event['id'], PDO::PARAM_INT);
$teamStmt->execute();
$team_members = $teamStmt->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>Team-Mitglieder für Event " . $event['id'] . ": ";
print_r($team_members); // Gibt die Team-Mitglieder für das Event aus
echo "</pre>";

// Wenn keine Team-Mitglieder vorhanden sind, setze es auf ein leeres Array
if (empty($team_members)) {
    $event['team_members'] = [];  // Leeres Array statt null
} else {
    $event['team_members'] = $team_members;
}

// Event ausgeben, nachdem Team-Mitglieder zugeordnet wurden
echo "<pre>Event nach der Team-Mitglieder-Zuordnung: ";
print_r($event); 
echo "</pre>";
}

// Überprüfe, ob alle Events korrekt ausgegeben werden
echo "<pre>Events nach der Team-Mitglieder-Zuordnung: ";
print_r($events);
echo "</pre>";
?>

<!-- Ausgabe der Events -->
<table class="table table-striped projects">
    <thead>
        <tr>
            <th style="width: 1%">#</th>
            <th style="width: 20%">Ansprechpartner</th>
            <th style="width: 20%">Event</th>
            <th style="width: 20%">Anmerkung</th>
            <th style="width: 30%">Team Members</th>
            <th>Status</th>
            <th style="width: 20%">Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php
    // Ausgabe der Events und Teammitglieder
    foreach ($events as $event) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($event['id']) . "</td>";
        echo "<td><a>" . htmlspecialchars($event['vorname_nachname']) . "</a><br/><small>Created " . date('d.m.Y', strtotime($event['datum_uhrzeit'])) . "</small></td>";
        echo "<td><span>" . htmlspecialchars($event['event']) . "</span></td>";
        echo "<td><span>" . htmlspecialchars($event['anmerkung']) . "</span></td>";

        // Team-Mitglieder anzeigen
        echo "<td><ul class='list-inline'>";
        if (empty($event['team_members'])) {
            echo "<li>No team members available</li>";
        } else {
            foreach ($event['team_members'] as $member) {
                echo "<li class='list-inline-item' data-toggle='tooltip' title='" . htmlspecialchars($member['name']) . "'>";
                echo "<img alt='Avatar' class='table-avatar' src='" . htmlspecialchars($member['profile_image']) . "'>";
                echo "</li>";
            }
        }
        echo "</ul></td>";

        // Status anzeigen
        echo "<td class='project-state'>";
        if ($event['status'] == 'in Planung') {
            echo "<span class='badge badge-warning'>In Planung</span>";
        } elseif ($event['status'] == 'in Durchführung') {
            echo "<span class='badge badge-danger'>In Durchführung</span>";
        } elseif ($event['status'] == 'Abgeschlossen') {
            echo "<span class='badge badge-success'>Abgeschlossen</span>";
        }
        echo "</td>";

        // Aktionen
        echo "<td class='project-actions text-right'>";
        echo "<a class='btn btn-primary btn-sm' href='eventplanung_akte.php?id=" . $event['id'] . "'><i class='fas fa-folder'></i> View</a>";
        echo "<a class='btn btn-info btn-sm' href='#'><i class='fas fa-pencil-alt'></i> Edit</a>";
        echo "<a class='btn btn-danger btn-sm' href='#'><i class='fas fa-trash'></i> Delete</a>";
        echo "</td>";
        echo "</tr>";
    }
    ?>
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
