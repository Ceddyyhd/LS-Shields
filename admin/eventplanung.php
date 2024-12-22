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
          <div class="card-header">
            <h3 class="card-title">Expandable Table</h3>
          </div>
          <!-- ./card-header -->
          <div class="card-body">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Ansprechpartner</th>
                  <th>Event</th>
                  <th>Status</th>
                  <th>Info</th>
                </tr>
              </thead>
              <tbody>
                <tr data-widget="expandable-table" aria-expanded="false">
                  <td>1</td>
                  <td>Tom Meyer</td>
                  <td>SSC Offroad Rennen</td>
                  <td>geplant</td>
                  <td>ggf. Absicherung von Strecken. Planung bis 16.12</td>
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
                        <strong>Eventlead:</strong>
                        <div>Name: Sarah Müller</div>
                        <div>Tel. Nr.: 987654321</div>
                      </div>
                      <div class="mb-3">
                        <strong>Eingetragene Mitarbeiter:</strong>
                        <ul class="mb-0">
                          <li>Cedric Schmidt</li>
                          <li>Falco Hunter</li>
                        </ul>
                      </div>
                      <div>
                        <strong>Details:</strong>
                        <p>
                          Ggf. Absicherung der Strecke. Hauptsächlich Absicherung des Events, 
                          wo mindestens 10 Firmen (Catering, VR, Bühne ...) stehen. Zuschauertribüne.
                          Genauere Planung sollte bis zum 16.12 fertiggestellt sein.
                        </p>
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
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>
