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

// SQL-Abfrage, um den Namen des Mitarbeiters zu holen
$query = "
    SELECT v.start_date, v.end_date, v.status, u.name 
    FROM vacations v
    JOIN users u ON v.user_id = u.id
    WHERE v.status IN ('approved', 'pending')
";

$stmt = $conn->prepare($query);
$stmt->execute();

// Array für die Events
$events = [];

while ($vacation = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Setze die Hintergrundfarbe basierend auf dem Status
    $backgroundColor = ($vacation['status'] === 'approved') ? '#00a65a' : '#f39c12'; // Grün für 'approved', Gelb für 'pending'
    $borderColor     = $backgroundColor; // Border-Farbe gleich der Hintergrundfarbe

    $events[] = [
        'title' => 'Urlaub - ' . htmlspecialchars($vacation['name']), // Titel: Urlaub - Mitarbeiter Name
        'start' => $vacation['start_date'],
        'end'   => date('Y-m-d', strtotime($vacation['end_date'] . ' +1 day')), // Enddatum um einen Tag erhöhen
        'backgroundColor' => $backgroundColor,  // Farbe für "approved" oder "pending"
        'borderColor'     => $borderColor,     // Randfarbe gleich der Hintergrundfarbe
    ];
}

// Hier kannst du das Array $events weiterverwenden, um es in den Kalender zu laden
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
      <div class="col-md-3">
  <div class="sticky-top mb-3">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Create Event</h3>
      </div>
      <div class="card-body">
        <form id="vacationForm">
          <div class="form-group">
            <label>Start Datum</label>
            <input type="date" class="form-control" id="start_date" required>
          </div>
          <div class="form-group">
            <label>End Datum</label>
            <input type="date" class="form-control" id="end_date" required>
          </div>
          <button type="button" class="btn btn-primary" id="submitVacation">Urlaub Anmelden</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  document.getElementById('submitVacation').addEventListener('click', function () {
    var start_date = document.getElementById('start_date');
    var end_date = document.getElementById('end_date');
    
    // Überprüfe, ob die Elemente existieren
    if (start_date && end_date) {
        var start_date_value = start_date.value;
        var end_date_value = end_date.value;

        if (start_date_value && end_date_value) {
            var formData = new FormData();
            formData.append('start_date', start_date_value);
            formData.append('end_date', end_date_value);

            // AJAX-Anfrage, um den Urlaub zu speichern
            fetch('include/vacation_create.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Urlaub erfolgreich erstellt!');
                } else {
                    alert('Fehler: ' + data.message);
                }
            })
            .catch(error => {
                alert('Fehler: ' + error.message);
            });
        } else {
            alert('Bitte füllen Sie alle Felder aus!');
        }
    } else {
        console.error('Start- oder End-Datum-Element fehlt!');
    }
});
</script>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="card card-primary">
              <div class="card-body p-0">
                <!-- THE CALENDAR -->
                <div id="calendar"></div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
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
