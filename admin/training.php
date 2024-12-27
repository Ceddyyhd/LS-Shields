<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<?php include 'include/header.php'; ?>

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
        <?php if (isset($_SESSION['permissions']['create_trainings']) && $_SESSION['permissions']['create_trainings']): ?>
    <div class="card-header">
        <h3 class="card-title">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-user-create">
                Benutzer erstellen
            </button>
        </h3>
    </div>
<?php endif; ?> 
                
                
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
                <?php if (isset($_SESSION['permissions']['remove_trainings']) && $_SESSION['permissions']['remove_trainings']): ?>
                <th>Löschen</th>
            <?php endif; ?>
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
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>
