<!DOCTYPE html>
<html lang="en">
<?php include 'include/header.php'; 
// ID des aktuellen Benutzers aus der Session holen
$user_id = $_SESSION['user_id'];

// SQL-Abfrage, um nur die Urlaubsanträge des aktuellen Benutzers anzuzeigen, deren `end_date` heute oder in der Zukunft liegt
$query = "
    SELECT * FROM vacations 
    WHERE user_id = :user_id 
    AND end_date >= CURDATE()  -- Nur Anträge, deren Enddatum heute oder in der Zukunft liegt
    AND status IN ('approved', 'pending')
";

$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

// Alle Urlaubsanträge des Benutzers holen
$vacations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

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

// SQL-Abfrage, um die Urlaubs- und Eventdaten zu holen
$query = "
    SELECT v.start_date, v.end_date, v.status, u.name, NULL as event, NULL as datum_uhrzeit_event, NULL as event_id
    FROM vacations v
    JOIN users u ON v.user_id = u.id
    WHERE v.status IN ('approved', 'pending')
    UNION
    SELECT NULL as start_date, NULL as end_date, NULL as status, u.name, e.event as event, e.datum_uhrzeit_event, e.id as event_id
    FROM eventplanung e
    JOIN users u ON e.event_lead = u.id  -- Verknüpfen über die Spalte event_lead
    WHERE e.datum_uhrzeit_event IS NOT NULL
";

$stmt = $conn->prepare($query);
$stmt->execute();

// Array für die Events
$events = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Wenn es sich um ein Event handelt (aus der eventplanung-Tabelle)
    if ($row['datum_uhrzeit_event'] !== null) {
        $eventTitle = !empty($row['event']) ? 'Event: ' . htmlspecialchars($row['event']) : 'Event: in Planung';
        $startDate = $row['datum_uhrzeit_event'];
        $endDate = $row['datum_uhrzeit_event']; // Da kein Enddatum vorhanden, verwenden wir das Startdatum

        $backgroundColor = '#3498db'; // Blau für Event
        $borderColor = $backgroundColor; // Randfarbe gleich der Hintergrundfarbe

        // URL für das Event (Verlinkung zur Detailseite)
        $eventLink = 'eventplanung_akte.php?id=' . $row['event_id'];
    } else {
        // Wenn es sich um einen Urlaub handelt (aus der vacations-Tabelle)
        $eventTitle = 'Urlaub - ' . htmlspecialchars($row['name']);
        $startDate = $row['start_date'];
        $endDate = date('Y-m-d', strtotime($row['end_date'] . ' +1 day')); // Enddatum um einen Tag erhöhen

        $backgroundColor = ($row['status'] === 'approved') ? '#00a65a' : '#f39c12'; // Grün für 'approved', Gelb für 'pending'
        $borderColor = $backgroundColor; // Randfarbe gleich der Hintergrundfarbe

        // URL für das Event (verlinkt auf die Urlaubsseite, falls gewünscht)
        $eventLink = '#'; // Optional: Hier könnte eine andere URL für den Urlaub hinzugefügt werden
    }

    // Füge die URL als weiteres Feld hinzu
    $events[] = [
        'title' => $eventTitle,
        'start' => $startDate,
        'end' => $endDate,
        'backgroundColor' => $backgroundColor,
        'borderColor' => $borderColor,
        'url' => $eventLink, // Der Link zur Detailseite des Events
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
        <h3 class="card-title">Urlaub einreichen</h3>
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

              <!-- /.card-header -->
              <div class="card-body">
    <label>Urlaub zurückziehen</label>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
                <th>Entfernen</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vacations as $vacation): ?>
                <tr>
                    <td><?php echo $vacation['start_date']; ?></td>
                    <td><?php echo $vacation['end_date']; ?></td>
                    <td>
                        <span class="badge bg-<?php echo ($vacation['status'] == 'approved') ? 'success' : 'warning'; ?>">
                            <?php echo ucfirst($vacation['status']); ?>
                        </span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-block btn-outline-danger" onclick="deleteVacation(<?php echo $vacation['id']; ?>)">
                            X
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script>
    // Funktion zum Löschen des Urlaubsantrags
    function deleteVacation(vacationId) {
        if (confirm('Möchten Sie diesen Urlaub zurückziehen?')) {
            // AJAX-Anfrage zum Löschen des Urlaubsantrags
            var formData = new FormData();
            formData.append('vacation_id', vacationId);

            fetch('include/vacation_delete.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Urlaub erfolgreich zurückgezogen!');
                    location.reload();  // Seite neu laden, um den Antrag zu entfernen
                } else {
                    alert('Fehler: ' + data.message);
                }
            })
            .catch(error => {
                alert('Fehler: ' + error.message);
            });
        }
    }
</script>
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
