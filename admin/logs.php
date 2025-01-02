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
<!-- jQuery (notwendig für Bootstrap) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

  <!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Starter Page</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Starter Page</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  <style>
  @media (min-width: 576px) {
    .col-sm-6 {
        -ms-flex: 0 0 50%;
        flex: 0 0 100%;
        max-width: 100%;
    }
}
</style>
<?php
// Datenbankverbindung einbinden
include 'db.php';

// Logs aus der Datenbank abfragen
$stmt = $conn->prepare("SELECT * FROM anfragen_logs ORDER BY timestamp DESC");
$stmt->execute();
$anfrage_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
  <div class="row">
          <div class="col-12 col-sm-6">
            <div class="card card-primary card-tabs">
              <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="anfrage-logs-tab" data-toggle="pill" href="#anfrage-logs" role="tab" aria-controls="anfrage-logs" aria-selected="true">Anfrage Logs</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-one-profile-tab" data-toggle="pill" href="#custom-tabs-one-profile" role="tab" aria-controls="custom-tabs-one-profile" aria-selected="false">Profile</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-one-messages-tab" data-toggle="pill" href="#custom-tabs-one-messages" role="tab" aria-controls="custom-tabs-one-messages" aria-selected="false">Messages</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-one-settings-tab" data-toggle="pill" href="#custom-tabs-one-settings" role="tab" aria-controls="custom-tabs-one-settings" aria-selected="false">Settings</a>
                  </li>
                </ul>
              </div>
              <div class="card-body">
                <div class="tab-content" id="custom-tabs-one-tabContent">
                  <div class="tab-pane fade show active" id="anfrage-logs" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab">


                  <div class="card">
                    <div class="card-body">
                      <table id="example1" class="table table-bordered table-striped">
                        <thead>
                          <tr>
                            <th>id</th>
                            <th>action</th>
                            <th>timestamp</th>
                            <th>anfrage_id</th>
                            <th>username</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          // Schleife durch die Logs und Ausgabe der Daten in der Tabelle
                          foreach ($anfrage_logs as $anfrage_log) {
                              echo "<tr>";
                              echo "<td>" . htmlspecialchars($anfrage_log['id']) . "</td>";
                              echo "<td>" . htmlspecialchars($anfrage_log['action']) . "</td>";
                              echo "<td>" . htmlspecialchars($anfrage_log['timestamp']) . "</td>";
                              echo "<td>" . htmlspecialchars($anfrage_log['anfrage_id']) . "</td>";
                              echo "<td>" . htmlspecialchars($anfrage_log['username']) . "</td>";
                              echo "</tr>";
                          }
                          ?>
                        </tbody>
                        <tfoot>
                          <tr>
                            <th>id</th>
                            <th>action</th>
                            <th>timestamp</th>
                            <th>anfrage_id</th>
                            <th>username</th>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>


                </div>
                  <div class="tab-pane fade" id="custom-tabs-one-profile" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab">

                  <div class="card">
                    <div class="card-body">
                      <table id="example1" class="table table-bordered table-striped">
                        <thead>
                          <tr>
                            <th>id</th>
                            <th>action</th>
                            <th>timestamp</th>
                            <th>anfrage_id</th>
                            <th>username</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          // Schleife durch die Logs und Ausgabe der Daten in der Tabelle
                          foreach ($logs as $log) {
                              echo "<tr>";
                              echo "<td>" . htmlspecialchars($log['id']) . "</td>";
                              echo "<td>" . htmlspecialchars($log['action']) . "</td>";
                              echo "<td>" . htmlspecialchars($log['timestamp']) . "</td>";
                              echo "<td>" . htmlspecialchars($log['anfrage_id']) . "</td>";
                              echo "<td>" . htmlspecialchars($log['username']) . "</td>";
                              echo "</tr>";
                          }
                          ?>
                        </tbody>
                        <tfoot>
                          <tr>
                            <th>id</th>
                            <th>action</th>
                            <th>timestamp</th>
                            <th>anfrage_id</th>
                            <th>username</th>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>

                </div>
                  <div class="tab-pane fade" id="custom-tabs-one-messages" role="tabpanel" aria-labelledby="custom-tabs-one-messages-tab">
                     Morbi turpis dolor, vulputate vitae felis non, tincidunt congue mauris. Phasellus volutpat augue id mi placerat mollis. Vivamus faucibus eu massa eget condimentum. Fusce nec hendrerit sem, ac tristique nulla. Integer vestibulum orci odio. Cras nec augue ipsum. Suspendisse ut velit condimentum, mattis urna a, malesuada nunc. Curabitur eleifend facilisis velit finibus tristique. Nam vulputate, eros non luctus efficitur, ipsum odio volutpat massa, sit amet sollicitudin est libero sed ipsum. Nulla lacinia, ex vitae gravida fermentum, lectus ipsum gravida arcu, id fermentum metus arcu vel metus. Curabitur eget sem eu risus tincidunt eleifend ac ornare magna.
                  </div>
                  <div class="tab-pane fade" id="custom-tabs-one-settings" role="tabpanel" aria-labelledby="custom-tabs-one-settings-tab">
                     Pellentesque vestibulum commodo nibh nec blandit. Maecenas neque magna, iaculis tempus turpis ac, ornare sodales tellus. Mauris eget blandit dolor. Quisque tincidunt venenatis vulputate. Morbi euismod molestie tristique. Vestibulum consectetur dolor a vestibulum pharetra. Donec interdum placerat urna nec pharetra. Etiam eget dapibus orci, eget aliquet urna. Nunc at consequat diam. Nunc et felis ut nisl commodo dignissim. In hac habitasse platea dictumst. Praesent imperdiet accumsan ex sit amet facilisis.
                  </div>
                </div>
              </div>
              <!-- /.card -->
            </div>
          </div>
        </div>
</div>





<!-- JavaScript Section -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.6/js/dataTables.bootstrap4.min.js"></script>



  <!-- /.col -->
</div>
<!-- /.row -->
</div>
<!-- /.container-fluid -->
</section>
    
    
    

      


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
