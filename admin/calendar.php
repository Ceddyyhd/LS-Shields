<!DOCTYPE html>
<html lang="en">
<?php include 'include/header.php'; ?>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Calendar</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- fullCalendar -->
  <link rel="stylesheet" href="plugins/fullcalendar/main.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

<?php include 'include/navbar.php'; ?>

<?php
include 'include/db.php'; // Datenbankverbindung einbinden

// SQL-Abfrage, um die Urlaubsanfragen abzurufen (status = 'approved' wird gefiltert)
$query = "
    SELECT v.start_date, v.end_date, u.name 
    FROM vacations v
    JOIN users u ON v.user_id = u.id
    WHERE v.status = 'approved'
";

$stmt = $conn->prepare($query);
$stmt->execute();

// Array für die Events
$events = [];

while ($vacation = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $events[] = [
        'title' => 'Urlaub - ' . htmlspecialchars($vacation['name']), // Titel: Urlaub - Mitarbeiter Name
        'start' => $vacation['start_date'],
        'end'   => date('Y-m-d', strtotime($vacation['end_date'] . ' +1 day')), // Enddatum um einen Tag erhöhen
        'backgroundColor' => '#00a65a',  // Farbe für "Urlaub"
        'borderColor'     => '#00a65a',  // Farbe für den Rand des Events
    ];
}
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Urlaubsübersicht</h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            <div class="card card-primary">
              <div class="card-body p-0">
                <!-- THE CALENDAR -->
                <div id="calendar"></div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 3.2.0
    </div>
    <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- jQuery UI -->
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- fullCalendar 2.2.5 -->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/fullcalendar/main.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<!-- Page specific script -->
<script>
  $(function () {

    /* initialize the calendar
     -----------------------------------------------------------------*/
    var Calendar = FullCalendar.Calendar;

    var calendarEl = document.getElementById('calendar');

    // initialisiere den Kalender
    var calendar = new Calendar(calendarEl, {
      headerToolbar: {
        left  : 'prev,next today',
        center: 'title',
        right : 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      themeSystem: 'bootstrap',
      // Hier die Urlaubsereignisse dynamisch einfügen
      events: <?php echo json_encode($events); ?>,  // Die Events aus PHP werden hier ins JavaScript übergeben
      editable  : true,
      droppable : true  // Dies erlaubt es, Events zu verschieben
    });

    calendar.render();
  })
</script>
</body>
</html>
