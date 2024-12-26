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
// DB-Verbindung einbinden
include 'include/db.php'; // Stelle sicher, dass der Pfad korrekt ist

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

<!-- HTML-Ausgabe -->
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Event-Liste</h1>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th>Ansprechpartner</th>
            <th>Event</th>
            <th>Anmerkung</th>
            <th>Status</th>
            <th>Datum</th>
            <th>Team Members</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($events as $event): ?>
            <tr>
                <td><?= htmlspecialchars($event['id']); ?></td>
                <td><?= htmlspecialchars($event['vorname_nachname']); ?></td>
                <td><?= htmlspecialchars($event['event']); ?></td>
                <td><?= htmlspecialchars($event['anmerkung']); ?></td>
                <td><?= htmlspecialchars($event['status']); ?></td>
                <td><?= date('d.m.Y H:i', strtotime($event['datum_uhrzeit'])); ?></td>
                <td>
                    <?php
                    if (!empty($event['team_members'])) {
                        foreach ($event['team_members'] as $member) {
                            echo htmlspecialchars($member['name']) . " ";
                            echo "<img src='" . htmlspecialchars($member['profile_image']) . "' alt='Avatar' width='30' height='30' />";
                        }
                    } else {
                        echo "Keine Teammitglieder";
                    }
                    ?>
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
