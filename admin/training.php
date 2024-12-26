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

                        <p>One fine body&hellip;</p>
                      
                        <label>Date and time:</label>
                        <div class="input-group date" id="reservationdatetime" data-target-input="nearest">
                            <input type="text" class="form-control datetimepicker-input" data-target="#reservationdatetime"/>
                            <div class="input-group-append" data-target="#reservationdatetime" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                          
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

                      </div>
                      <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                      </div>
                    </div>
                    <!-- /.modal-content -->
                  </div>
                  <!-- /.modal-dialog -->
                </div>


          </div>
          <!-- ./card-header -->
          <div class="card-body">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Datum & Uhrzeit</th>
                  <th>Grund</th>
                  <th>Trainingsleitung</th>
                  <th>Info</th>
                </tr>
              </thead>
              <tbody>
                <tr data-widget="expandable-table" aria-expanded="false">
                  <td>1</td>
                  <td>08.01.2024 19:00</td>
                  <td>Szenario Training</td>
                  <td>Aiden Knox, Paul Garcia</td>
                  <td>10 Minuten vor Start am Firmengelände in Dienstkleidung 
                    Genügend Essen und Trinken einpacken</td>
                </tr>
                <tr class="expandable-body">
                  <td colspan="5">
                    <div class="p-3">
                      <div class="mb-3">
                        <strong>Ansprechpartner:</strong>
                        <div>Name: Tom Meyer</div>
                        <div>Tel. Nr.: 123456789</div>
                      </div>
                      <div class="mb-3">
                        <strong>Eingetragene Mitarbeiter:</strong>
                        <ul class="mb-0">
                          <li>Cedric Schmidt</li>
                          <li>Falco Hunter</li>
                        </ul>
                      </div>
                    </div>
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
<!-- Bootstrap4 Duallistbox -->
<script src="plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
<!-- InputMask -->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/inputmask/jquery.inputmask.min.js"></script>
<!-- date-range-picker -->
<script src="plugins/daterangepicker/daterangepicker.js"></script>
</body>
</html>
