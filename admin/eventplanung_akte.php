<!DOCTYPE html>
<html lang="en">

<head>
<!-- summernote -->
<link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- daterange picker -->
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Bootstrap Color Picker -->
  <link rel="stylesheet" href="plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <!-- Bootstrap4 Duallistbox -->
  <link rel="stylesheet" href="plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">
  <!-- BS Stepper -->
  <link rel="stylesheet" href="plugins/bs-stepper/css/bs-stepper.min.css">
  <!-- dropzonejs -->
  <link rel="stylesheet" href="plugins/dropzone/min/dropzone.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote.min.js"></script>

  <?php
// Verbindung zur Datenbank einbinden
include('include/db.php');

// ID aus der URL holen
if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    die('Kein Eventplanungs-ID angegeben.');
}



// SQL-Abfrage zum Abrufen der Eventplanung aus der Datenbank
try {
    $stmt = $conn->prepare("SELECT * FROM eventplanung WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // Ergebnis abrufen
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        die('Eventplanung nicht gefunden.');
    }
} catch (PDOException $e) {
    die("Fehler beim Abrufen der Daten: " . $e->getMessage());
}
?>
<?php include 'include/header.php'; ?>

<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  <?php include 'include/navbar.php'; ?>

  <!-- /.navbar -->

  <!-- Main Sidebar Container -->


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Profile</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">User Profile</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle"
                       src="dist/img/user4-128x128.jpg"
                       alt="User profile picture">
                </div>
                <p class="text-muted text-center">Ansprechpartner</p>
                <h3 class="profile-username text-center">Nina Mcintire</h3>

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>Tel. Nr.:</b> <a class="float-right">555 - 667 7541</a>
                  </li>
                  <li class="list-group-item">
                    <b>Datum & Uhrzeit:</b> <a class="float-right">15.12.2024</a>
                  </li>
                  <li class="list-group-item">
                    <b>Ort:</b> <a class="float-right">15.12.2024</a>
                  </li>
                </ul>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <!-- About Me Box -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Informationen</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <strong><i class="fas fa-book mr-1"></i> Teams</strong>

                <div class="card-body">
                <dl class="row">
                  <dt class="col-sm-4">Haupteingang</dt>
                  <dd class="col-sm-8"> 
                    <ul>
                      <li>Cedric Schmidt</li>
                      <li>John Schmidt</li>
                    </ul>
                </dd>
                  <dt class="col-sm-4">Nebeneingang</dt>
                  <dd class="col-sm-8"> 
                    <ul>
                      <li>Cedric Schmidt</li>
                      <li>John Schmidt</li>
                    </ul>
                </dd>
                  <dt class="col-sm-4">Tür 1</dt>
                  <dd class="col-sm-8"> 
                    <ul>
                      <li>Cedric Schmidt</li>
                      <li>John Schmidt</li>
                    </ul>
                </dd>
                  <dt class="col-sm-4">Tür 2</dt>
                  <dd class="col-sm-8"> 
                    <ul>
                      <li>Cedric Schmidt</li>
                      <li>John Schmidt</li>
                    </ul>
                </dd>

                  </dd>
                </dl>
              </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#plan" data-toggle="tab">Plan</a></li>
                  <li class="nav-item"><a class="nav-link" href="#plan-bearbeiten" data-toggle="tab">Plan Bearbeiten</a></li>
                  <li class="nav-item"><a class="nav-link" href="#anmeldung" data-toggle="tab">Anmeldung</a></li>
                  <li class="nav-item"><a class="nav-link" href="#dienstplan" data-toggle="tab">Dienstplan</a></li>
                  <li class="nav-item"><a class="nav-link" href="#externes-dokument1" data-toggle="tab">Externes Dokument (Benennbar)</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                <div class="active tab-pane" id="plan">
                    <!-- Direkte Ausgabe des gespeicherten HTML-Inhalts -->
                    <?= $event['summernote_content'] ?>
                </div>

                  <div class="active tab-pane" id="plan-bearbeiten">
                  <form action="speichern_eventplanung_summernote.php" method="POST">
        <div class="form-group">
            <label for="summernote">Anfrage:</label>
            <textarea id="summernote" name="summernoteContent"><?= htmlspecialchars($event['summernote_content']) ?></textarea>
        </div>
        
        <input type="hidden" name="id" value="<?= $event['id'] ?>">
        
        <button type="button" id="submitForm" class="btn btn-danger">Speichern</button> <!-- Submit-Button -->
    </form>

    <script>
        $(document).ready(function() {
            // Summernote initialisieren
            $('#summernote').summernote({
                height: 300,   // Höhe von Summernote anpassen
                focus: true     // Fokus auf das Summernote-Feld setzen
            });

            // Submit-Button-Funktionalität
            $('#submitForm').on('click', function() {
                var summernoteContent = $('#summernote').val();  // Den Inhalt von Summernote holen
                console.log(summernoteContent);  // Ausgabe des Inhalts in der Konsole

                // AJAX-Anfrage zum Speichern der Daten
                $.ajax({
                    url: 'include/speichern_eventplanung_summernote.php',  // Das PHP-Skript zum Speichern
                    type: 'POST',
                    data: {
                        summernoteContent: summernoteContent,
                        id: <?= $event['id'] ?>  // ID des Events übergeben
                    },
                    success: function(response) {
                        console.log(response);  // Antwort des Servers in der Konsole
                        alert('Daten wurden gespeichert!');
                    },
                    error: function(xhr, status, error) {
                        console.log('Fehler:', error);  // Fehlerdetails in der Konsole
                        alert('Fehler beim Speichern der Daten!');
                    }
                });
            });
        });
    </script>






                  </div>
                  

                  <div class="tab-pane" id="anmeldung">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label>Eintragen wer kann</label>
                    <div class="form-group">
                        <select class="form-control" name="employee_list[]">
                            <?php
                            // Verbindung zur Datenbank
                            include('db.php');

                            // Event ID aus der URL holen
                            $eventId = $_GET['id'];

                            try {
                                // Alle Mitarbeiter holen, die sich noch nicht für das Event angemeldet haben
                                $stmt = $conn->prepare("
                                    SELECT u.id, u.name
                                    FROM users u
                                    WHERE u.gekuendigt = 'no_kuendigung'
                                    AND NOT EXISTS (
                                        SELECT 1 FROM event_mitarbeiter_anmeldung em
                                        WHERE em.employee_id = u.id AND em.event_id = :event_id
                                    )
                                ");
                                $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
                                $stmt->execute();
                                $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                // Mitarbeiter in die Dropdown-Liste einfügen
                                foreach ($employees as $employee) {
                                    echo '<option value="' . $employee['id'] . '">' . htmlspecialchars($employee['name']) . '</option>';
                                }
                            } catch (PDOException $e) {
                                echo 'Fehler: ' . $e->getMessage();
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button type="button" id="submitFormAnmeldung" class="btn btn-danger">Anmelden</button>
</div>


<script>
  $(document).ready(function() {
    // Anmeldung abschicken
    $('#submitFormAnmeldung').on('click', function() {
        var selectedEmployees = $('select[name="employee_list[]"]').val(); // Ausgewählte Mitarbeiter
        console.log('Ausgewählte Mitarbeiter:', selectedEmployees);  // Ausgabe der ausgewählten Mitarbeiter

        // Überprüfen, ob Mitarbeiter ausgewählt wurden
        if (selectedEmployees && selectedEmployees.length > 0) {
            console.log('Mitarbeiter werden gesendet');

            // Sicherstellen, dass selectedEmployees ein Array ist
            if (typeof selectedEmployees === 'string') {
                selectedEmployees = [selectedEmployees]; // Falls es nur eine Auswahl ist, in ein Array umwandeln
            }

            $.ajax({
                url: 'include/anmeldung_speichern.php', // PHP-Skript zum Speichern
                type: 'POST',
                data: {
                    event_id: <?= $_GET['id'] ?>,  // Event ID aus der URL
                    employees: selectedEmployees
                },
                success: function(response) {
                    console.log('Antwort vom Server:', response); // Serverantwort in der Konsole anzeigen
                    alert('Anmeldung erfolgreich!');

                    // Nach erfolgreicher Anmeldung den Mitarbeiter aus der Liste entfernen
                    selectedEmployees.forEach(function(employeeId) {
                        $('option[value="' + employeeId + '"]').remove();
                    });
                },
                error: function(xhr, status, error) {
                    console.log('AJAX-Fehler: ', error);  // Fehlerdetails in der Konsole
                    console.log('Status: ', status);
                    console.log('XHR-Objekt: ', xhr);
                    alert('Fehler bei der Anmeldung!');
                }
            });
        } else {
            alert('Bitte wählen Sie mindestens einen Mitarbeiter aus!');
        }
    });
});
</script>


<div class="tab-pane" id="dienstplan">
    <form class="form-horizontal" method="POST" action="include/save_dienstplan.php?id=<?php echo $_GET['id']; ?>">
        <?php
        // Verbindung zur Datenbank
        include('db.php');

        // Event ID aus der URL holen
        $eventId = $_GET['id'];

        try {
            // Alle Mitarbeiter holen, die sich für das Event angemeldet haben
            $stmt = $conn->prepare("
                SELECT u.id, u.name
                FROM users u
                JOIN event_mitarbeiter_anmeldung em ON em.employee_id = u.id
                WHERE em.event_id = :event_id
            ");
            $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
            $stmt->execute();
            $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Über alle Mitarbeiter iterieren und für jeden Mitarbeiter ein Formular erstellen
            foreach ($employees as $employee) {
                ?>
                <h4><?php echo htmlspecialchars($employee['name']); ?></h4>
                <div class="form-group">
                    <div class="bootstrap-timepicker">
                        <div class="form-group">
                            <label>Maximal da bis:</label>
                            <div class="input-group date" id="timepicker<?php echo $employee['id']; ?>" data-target-input="nearest">
                                <input type="text" class="form-control datetimepicker-input" data-target="#timepicker<?php echo $employee['id']; ?>" name="max_time_<?php echo $employee['id']; ?>"/>
                                <div class="input-group-append" data-target="#timepicker<?php echo $employee['id']; ?>" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="far fa-clock"></i></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Gearbeitete Zeit:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-clock"></i></span>
                                </div>
                                <input type="text" class="form-control float-right" name="work_time_<?php echo $employee['id']; ?>" id="reservationtime<?php echo $employee['id']; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        } catch (PDOException $e) {
            echo 'Fehler: ' . $e->getMessage();
        }
        ?>

        <div class="form-group row">
            <div class="offset-sm-2 col-sm-10">
            <button type="button" id="submitFormDienstplanung" class="btn btn-danger">Speichern</button>
            </div>
        </div>
    </form>
</div>

<script>
  $(document).ready(function() {
    // Submit-Button für den Dienstplan
    $('#submitForm').on('click', function() {
        var valid = true;

        // Überprüfen, ob die gearbeitete Zeit ausgefüllt wurde, falls sie nicht leer ist
        $('input[name^="work_time_"]').each(function() {
            if ($(this).val() !== '' && $(this).val() === '') {
                valid = false;
                alert('Bitte geben Sie die gearbeitete Zeit für alle Mitarbeiter ein.');
                return false; // Stoppt die Schleife, wenn eine Eingabe fehlt
            }
        });

        // Überprüfen der maximalen Zeit nur, wenn sie nicht leer ist
        $('input[name^="max_time_"]').each(function() {
            if ($(this).val() !== '' && $(this).val() === '') {
                valid = false;
                alert('Bitte geben Sie die maximale Zeit für alle Mitarbeiter ein.');
                return false; // Stoppt die Schleife, wenn eine Eingabe fehlt
            }
        });

        // Wenn alle Felder validiert sind, Formular absenden
        if (valid) {
            var formData = $('form').serialize();  // Alle Formulardaten sammeln

            $.ajax({
                url: 'include/save_dienstplan.php?id=<?php echo $_GET['id']; ?>',  // PHP-Skript zum Speichern
                type: 'POST',
                data: formData,  // Alle Formulardaten senden
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'success') {
                        alert(response.message);  // Erfolgsmeldung anzeigen
                    } else {
                        alert('Fehler: ' + response.message);  // Fehlermeldung anzeigen
                    }
                },
                error: function(xhr, status, error) {
                    alert('Fehler bei der Anfrage!');
                }
            });
        }
    });
});
</script>

<script>
  $(document).ready(function() {
    // Initialisiere datetimepicker für max_time
    <?php foreach ($employees as $employee) { ?>
        $('#timepicker<?php echo $employee['id']; ?>').datetimepicker({
            format: 'HH:mm'
        });
    <?php } ?>

    // Initialisiere datetimepicker für gearbeitete Zeit
    <?php foreach ($employees as $employee) { ?>
        $('#reservationtime<?php echo $employee['id']; ?>').datetimepicker({
            format: 'HH:mm'  // Verwende das passende Format
        });
    <?php } ?>
});
</script>


                  <div class="tab-pane" id="externes-dokument1">
                  <iframe src=""width="100%" height="700px"></iframe>                  
                </div>
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
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
<!-- Summernote -->
<script src="plugins/summernote/summernote-bs4.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Select2 -->
<script src="plugins/select2/js/select2.full.min.js"></script>
<!-- Bootstrap4 Duallistbox -->
<script src="plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
<!-- InputMask -->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/inputmask/jquery.inputmask.min.js"></script>
<!-- date-range-picker -->
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<!-- bootstrap color picker -->
<script src="plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Bootstrap Switch -->
<script src="plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<!-- BS-Stepper -->
<script src="plugins/bs-stepper/js/bs-stepper.min.js"></script>
<!-- dropzonejs -->
<script src="plugins/dropzone/min/dropzone.min.js"></script>
<script>
  $(function () {
    // Summernote
    $('#summernote').summernote({
        height:500,
    })
    //Date picker
    $('#reservationdate').datetimepicker({
        format: 'L'
    });

    //Date and time picker
    $('#reservationdatetime').datetimepicker({ icons: { time: 'far fa-clock' } });

    //Date range picker
    $('#reservation').daterangepicker()
    //Date range picker with time picker
    $('#reservationtime').daterangepicker({
      timePicker: true,
      timePickerIncrement: 30,
      locale: {
        format: 'MM/DD/YYYY hh:mm A'
      }
    })
    //Date range as a button
    $('#daterange-btn').daterangepicker(
      {
        ranges   : {
          'Today'       : [moment(), moment()],
          'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month'  : [moment().startOf('month'), moment().endOf('month')],
          'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(29, 'days'),
        endDate  : moment()
      },
      function (start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
      }
    )

    //Timepicker
    $('#timepicker').datetimepicker({
      format: 'LT'
    })
    //Bootstrap Duallistbox
    $('select.duallistbox').bootstrapDualListbox({
        moveOnSelect: false
    });
  })
</script>
</body>
</html>