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
// SQL-Abfrage zum Abrufen aller Events ohne Duplikate und NULL-Werte
$query = "
    SELECT eventplanung.id, 
           eventplanung.event, 
           eventplanung.anmerkung, 
           eventplanung.status, 
           eventplanung.vorname_nachname, 
           eventplanung.datum_uhrzeit
    FROM eventplanung"; 

$stmt = $conn->prepare($query);
$stmt->execute();

// Alle Events abrufen
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Debugging: Alle abgerufenen Events ausgeben
echo '<pre>';
print_r($events); // Gibt alle abgerufenen Events aus
echo '</pre>';

// Teammitglieder für jedes Event abfragen und doppelte IDs vermeiden
foreach ($events as &$event) {
    // Teammitglieder abfragen
    $teamQuery = "
    SELECT DISTINCT eam.event_id, u.name, u.profile_image
    FROM event_mitarbeiter_anmeldung eam
    JOIN users u ON eam.employee_id = u.id
    WHERE eam.event_id = :event_id";

$teamStmt = $conn->prepare($teamQuery);
$teamStmt->bindParam(':event_id', $event['id'], PDO::PARAM_INT);
$teamStmt->execute();
$team_members = $teamStmt->fetchAll(PDO::FETCH_ASSOC);

// Debugging: Ausgabe der abgerufenen Teammitglieder
echo "<pre>";
print_r($team_members); // Gibt alle abgerufenen Teammitglieder aus
echo "</pre>";

    // Teammitglieder in das Event-Datenfeld einfügen
    $event['team_members'] = $team_members;
}
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
// Beispiel für korrektes Handling der Event-IDs und Teammitglieder

$events = array(); // Deine Events aus der DB
$team_members = array(); // Deine Team-Mitglieder aus der DB

// Beispiel-Daten - Ersetze dies mit der richtigen Logik
$events = [
    ['id' => 1, 'event' => 'SS offroad rennen', 'anmerkung' => '...', 'status' => 'in Planung', 'vorname_nachname' => 'Cedric Schmidt', 'datum_uhrzeit' => '2024-12-22 23:54:37'],
    ['id' => 2, 'event' => 'test', 'anmerkung' => '', 'status' => 'in Planung', 'vorname_nachname' => 'dede', 'datum_uhrzeit' => '2024-12-22 23:53:54'],
    ['id' => 7, 'event' => 'test2', 'anmerkung' => '', 'status' => 'in Planung', 'vorname_nachname' => 'Test', 'datum_uhrzeit' => '2024-12-24 23:59:13'],
    ['id' => 8, 'event' => '', 'anmerkung' => '', 'status' => 'in Planung', 'vorname_nachname' => 'dfr', 'datum_uhrzeit' => '2024-12-24 22:57:39']
];

$team_members = [
    ['event_id' => 1, 'name' => 'Cedric Schmidt', 'profile_image' => 'uploads/profile_images/standard.png'],
    ['event_id' => 1, 'name' => 'testtttestttddedede', 'profile_image' => 'uploads/profile_images/standard.png'],
    ['event_id' => 1, 'name' => 'Cedric S.', 'profile_image' => 'uploads/profile_images/standard.png']
];

// Sicherstellen, dass keine doppelten Events in der Ausgabe erscheinen
$unique_events = array_unique($events, SORT_REGULAR);

// Ausgabe der Events und Teammitglieder
foreach ($unique_events as $event) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($event['id']) . "</td>";
    echo "<td><a>" . htmlspecialchars($event['vorname_nachname']) . "</a><br/><small>Created " . date('d.m.Y', strtotime($event['datum_uhrzeit'])) . "</small></td>";
    echo "<td><span>" . htmlspecialchars($event['event']) . "</span></td>";
    echo "<td><span>" . htmlspecialchars($event['anmerkung']) . "</span></td>";

    // Teammitglieder anzeigen
    echo "<td><ul class='list-inline'>";
    $has_team_members = false;
    foreach ($team_members as $member) {
        if ($member['event_id'] == $event['id']) {
            echo "<li class='list-inline-item' data-toggle='tooltip' title='" . htmlspecialchars($member['name']) . "'>";
            echo "<img alt='Avatar' class='table-avatar' src='" . htmlspecialchars($member['profile_image']) . "'>";
            echo "</li>";
            $has_team_members = true;
        }
    }
    if (!$has_team_members) {
        echo "<li>No team members available</li>";
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
