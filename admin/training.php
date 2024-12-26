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
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-training-erstellen">
                  Training Erstellen
                </button>      
                
                
                <div class="modal fade" id="modal-training-erstellen">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 class="modal-title">Training Erstellen</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <div class="form-group">
                            <label for="trainingGrund">Grund</label>
                            <input type="text" class="form-control" id="trainingGrund" placeholder="Enter Grund">
                        </div>
                        <div class="form-group">
                            <label for="trainingInfo">Info</label>
                            <input type="text" class="form-control" id="trainingInfo" placeholder="Enter Info">
                        </div>
                        <div class="form-group">
                            <label for="trainingLeitung">Trainingsleitung</label>
                            <input type="text" class="form-control" id="trainingLeitung" placeholder="Enter Leitung">
                        </div>

                        <label>Date and time:</label>
                        <div class="input-group date" id="reservationdatetime" data-target-input="nearest">
                            <input type="text" class="form-control datetimepicker-input" data-target="#reservationdatetime" id="trainingDate"/>
                            <div class="input-group-append" data-target="#reservationdatetime" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="saveTraining">Save changes</button>
            </div>
                    </div>
                    <!-- /.modal-content -->
                  </div>
                  <!-- /.modal-dialog -->
                </div>


          </div>
          <!-- ./card-header -->
          <div class="card-body">
          <table class="table table-bordered table-hover" id="trainingTable">
              <thead>
                  <tr>
                      <th>#</th>
                      <th>Datum & Uhrzeit</th>
                      <th>Grund</th>
                      <th>Trainingsleitung</th>
                      <th>Info</th>
                      <th>An/Abmeldung</th>
                  </tr>
              </thead>
              <tbody id="trainingList">
                  <!-- Dynamische Inhalte -->
              </tbody>
          </table>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
    </div>
    
    
    <script>
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
                loadTrainings(); // Trainings neu laden
                $('#modal-training-erstellen').modal('hide');
            }
        }
    });
});

// Trainings abrufen und anzeigen
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
                var row = '<tr>' +
                    '<td>' + training.id + '</td>' +
                    '<td>' + training.datum_zeit + '</td>' +
                    '<td>' + training.grund + '</td>' +
                    '<td>' + training.leitung + '</td>' +
                    '<td>' + training.info + '</td>' +
                    '<td>' +
                        '<button type="button" class="btn btn-block btn-primary" onclick="toggleAnmeldung(' + training.id + ')">Anmelden</button>' +
                        '<button type="button" class="btn btn-block btn-danger" onclick="toggleAbmeldung(' + training.id + ')">Abmelden</button>' +
                    '</td>' +
                '</tr>';
                tableBody.append(row);
            });
        }
    });
}

// Anmeldung
function toggleAnmeldung(trainingId) {
    $.ajax({
        url: 'include/training_anmeldung.php',
        method: 'POST',
        data: {
            action: 'anmelden',
            training_id: trainingId
        },
        success: function() {
            loadTrainings(); // Trainings neu laden
        }
    });
}

// Abmeldung
function toggleAbmeldung(trainingId) {
    $.ajax({
        url: 'include/training_anmeldung.php',
        method: 'POST',
        data: {
            action: 'abmelden',
            training_id: trainingId
        },
        success: function() {
            loadTrainings(); // Trainings neu laden
        }
    });
}

// Trainings beim Laden der Seite anzeigen
$(document).ready(function() {
    loadTrainings();
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
