<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<?php include 'include/header.php'; ?>
<header>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- daterange picker -->
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- Bootstrap4 Duallistbox -->
  <link rel="stylesheet" href="plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote.min.js"></script>
</header>
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

    <!-- Main content -->
    
    <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Expandable Table</h3>
              </div>
              <!-- ./card-header -->
              <div class="card-body">
                <table class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>User</th>
                      <th>Date</th>
                      <th>Status</th>
                      <th>Reason</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr data-widget="expandable-table" aria-expanded="false">
                      <td>183</td>
                      <td>John Doe</td>
                      <td>11-7-2014</td>
                      <td>Approved</td>
                      <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
                    </tr>
                    <tr class="expandable-body">
                      <td colspan="5">
                        <p>
                          Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
                        </p>
                      </td>
                    </tr>
                    <tr data-widget="expandable-table" aria-expanded="true">
                      <td>219</td>
                      <td>Alexander Pierce</td>
                      <td>11-7-2014</td>
                      <td>Pending</td>
                      <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
                    </tr>
                    <tr class="expandable-body">
                      <td colspan="5">
                        <p>
                          Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
                        </p>
                      </td>
                    </tr>
                    <tr data-widget="expandable-table" aria-expanded="true">
                      <td>657</td>
                      <td>Alexander Pierce</td>
                      <td>11-7-2014</td>
                      <td>Approved</td>
                      <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
                    </tr>
                    <tr class="expandable-body">
                      <td colspan="5">
                        <p>
                          Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
                        </p>
                      </td>
                    </tr>
                    <tr data-widget="expandable-table" aria-expanded="false">
                      <td>175</td>
                      <td>Mike Doe</td>
                      <td>11-7-2014</td>
                      <td>Denied</td>
                      <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
                    </tr>
                    <tr class="expandable-body">
                      <td colspan="5">
                        <p>
                          Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
                        </p>
                      </td>
                    </tr>
                    <tr data-widget="expandable-table" aria-expanded="false">
                      <td>134</td>
                      <td>Jim Doe</td>
                      <td>11-7-2014</td>
                      <td>Approved</td>
                      <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
                    </tr>
                    <tr class="expandable-body">
                      <td colspan="5">
                        <p>
                          Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
                        </p>
                      </td>
                    </tr>
                    <tr data-widget="expandable-table" aria-expanded="false">
                      <td>494</td>
                      <td>Victoria Doe</td>
                      <td>11-7-2014</td>
                      <td>Pending</td>
                      <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
                    </tr>
                    <tr class="expandable-body">
                      <td colspan="5">
                        <p>
                          Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
                        </p>
                      </td>
                    </tr>
                    <tr data-widget="expandable-table" aria-expanded="false">
                      <td>832</td>
                      <td>Michael Doe</td>
                      <td>11-7-2014</td>
                      <td>Approved</td>
                      <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
                    </tr>
                    <tr class="expandable-body">
                      <td colspan="5">
                        <p>
                          Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
                        </p>
                      </td>
                    </tr>
                    <tr data-widget="expandable-table" aria-expanded="false">
                      <td>982</td>
                      <td>Rocky Doe</td>
                      <td>11-7-2014</td>
                      <td>Denied</td>
                      <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
                    </tr>
                    <tr class="expandable-body">
                      <td colspan="5">
                        <p>
                          Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
                        </p>
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


<!-- Unsichtbares div mit data-username -->
<div id="user-info" data-username="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" style="display:none;"></div>

<script>
$(document).ready(function() {
    // Hole den Benutzernamen aus dem `data-username`-Attribut im HTML
    var username = $('#user-info').data('username');  // Benutzernamen aus data-Attribut holen

    // Initialisiere den DateTimePicker für das Erstellen des Trainings
    $('#reservationdatetime').datetimepicker({
        format: 'YYYY-MM-DD HH:mm',
        icons: {
            time: 'fa fa-clock',
            date: 'fa fa-calendar',
            up: 'fa fa-arrow-up',
            down: 'fa fa-arrow-down',
            previous: 'fa fa-chevron-left',
            next: 'fa fa-chevron-right'
        }
    });

    // Trainings speichern
    document.getElementById('saveTraining').addEventListener('click', function() {
        var grund = document.getElementById('trainingGrund').value;
        var info = document.getElementById('trainingInfo').value;
        var leitung = document.getElementById('trainingLeitung').value;
        var datum_zeit = document.getElementById('trainingDate').value;

        $.ajax({
            url: 'include/training_anmeldung.php',
            method: 'POST',
            data: {
                action: 'training_erstellen',
                grund: grund,
                info: info,
                leitung: leitung,
                datum_zeit: datum_zeit
            },
            success: function(response) {
                var result = JSON.parse(response);
                if (result.status === 'erfolgreich') {
                    loadTrainings(); // Trainingsliste neu laden
                    $('#modal-training-erstellen').modal('hide');
                } else {
                    console.error('Fehler beim Erstellen des Trainings:', result.error);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX-Fehler:', error);
            }
        });
    });

    // Trainings abrufen und anzeigen
    loadTrainings(); // Direkt beim Laden der Seite

    function loadTrainings() {
        $.ajax({
            url: 'include/training_anmeldung.php',
            method: 'POST',
            data: { action: 'get_trainings' },
            success: function(response) {
                var trainings = JSON.parse(response);
                var tableBody = $('#trainingList');
                tableBody.empty(); // Tabelle leeren

                trainings.forEach(function(training) {
                    // Anmelde- und Abmelde-Buttons erstellen
                    var anmeldenBtn = '<button type="button" class="btn btn-block btn-primary" onclick="toggleAnmeldung(' + training.id + ')">Anmelden</button>';
                    var abmeldenBtn = '<button type="button" class="btn btn-block btn-danger" onclick="toggleAbmeldung(' + training.id + ')">Abmelden</button>';

                    // Überprüfen, ob der Benutzer für das Training angemeldet ist
                    var actionButtons = '';
                    if (training.is_enrolled) {
                        actionButtons = abmeldenBtn; // Zeige Abmelden-Button, wenn angemeldet
                    } else {
                        actionButtons = anmeldenBtn; // Zeige Anmelden-Button, wenn nicht angemeldet
                    }

                    // Löschen-Button nur anzeigen, wenn der Benutzer die Berechtigung hat
                    var deleteButton = '';
                    <?php if (isset($_SESSION['permissions']['remove_trainings']) && $_SESSION['permissions']['remove_trainings']): ?>
                        deleteButton = '<button type="button" class="btn btn-block btn-danger" onclick="deleteTraining(' + training.id + ')">Löschen</button>';
                    <?php endif; ?>

                    // Zeile für das Training
                    var row = '<tr class="training-row" data-widget="expandable-table" aria-expanded="false">' +
                        '<td>' + training.id + '</td>' +
                        '<td>' + training.datum_zeit + '</td>' +
                        '<td>' + training.grund + '</td>' +
                        '<td>' + training.leitung + '</td>' +
                        '<td>' + training.info + '</td>' +
                        '<td>' + actionButtons + '</td>' +
                        '<td>' + deleteButton + '</td>' +
                        '</tr>';

                    // Dynamisch die eingetragenen Mitarbeiter abrufen (aus der `mitarbeiter`-Eigenschaft)
                    var mitarbeiterListe = '';
                    if (training.mitarbeiter) {
                        training.mitarbeiter.forEach(function(mitarbeiter) {
                            mitarbeiterListe += '<li>' + mitarbeiter.benutzername + '</li>';
                        });
                    }

                    // Zeile für die Details, die initial verborgen ist
                    var detailsRow = '<tr class="expandable-body" style="display:none;">' + // Initial verborgen
                        '<td colspan="6">' +  // Spaltenanzahl anpassen
                            '<div class="p-3">' +
                                '<div class="mb-3">' +
                                    '<strong>Eingetragene Mitarbeiter:</strong>' +
                                    '<ul class="mb-0">' +
                                        mitarbeiterListe +
                                    '</ul>' +
                                '</div>' +
                            '</div>' +
                        '</td>' +
                    '</tr>';

                    // Training und Details zur Tabelle hinzufügen
                    tableBody.append(row);
                    tableBody.append(detailsRow);
                });

                // Event-Listener für expandierende Zeilen (Toggle für expandieren und reduzieren)
                $('.training-row').on('click', function() {
                    var $this = $(this);
                    var $expandableRow = $this.next('.expandable-body');  // Nächste Zeile, die die Details enthält
                    
                    // Toggle die Sichtbarkeit der Details-Zeile
                    $expandableRow.toggle(); // Toggle der Anzeige
                });
            }
        });
    }

    // Funktion zum Löschen eines Trainings
    window.deleteTraining = function(trainingId) {
        if (confirm("Möchten Sie dieses Training wirklich löschen?")) {
            $.ajax({
                url: 'include/training_anmeldung.php',
                method: 'POST',
                data: {
                    action: 'delete_training',
                    training_id: trainingId
                },
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result.status === 'erfolgreich') {
                        loadTrainings(); // Trainingsliste neu laden
                    } else {
                        console.error('Fehler beim Löschen des Trainings:', result.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX-Fehler:', error);
                }
            });
        }
    }

    // Anmeldung - Diese Funktion wird aufgerufen, wenn der Benutzer auf "Anmelden" klickt
    window.toggleAnmeldung = function(trainingId) {
        $.ajax({
            url: 'include/training_anmeldung.php',
            method: 'POST',
            data: {
                action: 'anmelden',
                training_id: trainingId,
                benutzername: username // Den Benutzernamen übergeben
            },
            success: function(response) {
                var result = JSON.parse(response);
                if (result.status === 'angemeldet') {
                    loadTrainings(); // Trainingsliste neu laden
                } else {
                    console.error('Fehler beim Anmelden:', result.error);
                }
            }
        });
    }

    // Abmeldung - Diese Funktion wird aufgerufen, wenn der Benutzer auf "Abmelden" klickt
    window.toggleAbmeldung = function(trainingId) {
        $.ajax({
            url: 'include/training_anmeldung.php',
            method: 'POST',
            data: {
                action: 'abmelden',
                training_id: trainingId,
                benutzername: username // Den Benutzernamen übergeben
            },
            success: function(response) {
                var result = JSON.parse(response);
                if (result.status === 'abgemeldet') {
                    loadTrainings(); // Trainingsliste neu laden
                } else {
                    console.error('Fehler beim Abmelden:', result.error);
                }
            }
        });
    }
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



<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Summernote -->
<script src="plugins/summernote/summernote-bs4.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Bootstrap4 Duallistbox -->
<script src="plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
<!-- InputMask -->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/inputmask/jquery.inputmask.min.js"></script>
<!-- date-range-picker -->
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<script>
  $(function () {
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
      format: 'YYYY-MM-DD HH:mm', // MySQL-kompatibles Format
            
    })

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