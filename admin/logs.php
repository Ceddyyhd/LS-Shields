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
include 'include/db.php';

// Logs aus der Datenbank abfragen
$stmt = $conn->prepare("SELECT * FROM anfragen_logs ORDER BY timestamp DESC");
$stmt->execute();
$anfrage_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Logs aus der Datenbank abfragen
$stmt = $conn->prepare("SELECT * FROM ausbildung_logs ORDER BY timestamp DESC");
$stmt->execute();
$ausbildung_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Logs aus der Datenbank abfragen
$stmt = $conn->prepare("SELECT * FROM ausruestung_logs ORDER BY timestamp DESC");
$stmt->execute();
$ausruestung_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Logs aus der Datenbank abfragen
$stmt = $conn->prepare("SELECT * FROM kunden_logs ORDER BY timestamp DESC");
$stmt->execute();
$kunden_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                    <a class="nav-link" id="ausbildungs-logs-tab" data-toggle="pill" href="#ausbildungs-logs" role="tab" aria-controls="ausbildungs-logs" aria-selected="false">Ausbildungs Logs</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="ausruestung-logs-tab" data-toggle="pill" href="#ausruestung-logs" role="tab" aria-controls="ausruestung-logs" aria-selected="false">Ausruestung Logs</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="kunden-logs-tab" data-toggle="pill" href="#kunden-logs" role="tab" aria-controls="kunden-logs" aria-selected="false">Kunden Logs</a>
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
                  <div class="tab-pane fade" id="ausbildungs-logs" role="tabpanel" aria-labelledby="ausbildungs-logs-tab">

                  <div class="card">
                    <div class="card-body">
                      <table id="example1" class="table table-bordered table-striped">
                      <thead>
                          <tr>
                            <th>id</th>
                            <th>user_id</th>
                            <th>editor_name</th>
                            <th>ausbildung</th>
                            <th>action</th>
                            <th>timestamp</th>
                            <th>rating</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          // Schleife durch die Logs und Ausgabe der Daten in der Tabelle
                          foreach ($ausbildung_logs as $ausbildung_log) {
                              echo "<tr>";
                              echo "<td>" . htmlspecialchars($ausbildung_log['id']) . "</td>";
                              echo "<td>" . htmlspecialchars($ausbildung_log['user_id']) . "</td>";
                              echo "<td>" . htmlspecialchars($ausbildung_log['editor_name']) . "</td>";
                              echo "<td>" . htmlspecialchars($ausbildung_log['ausbildung']) . "</td>";
                              echo "<td>" . htmlspecialchars($ausbildung_log['action']) . "</td>";
                              echo "<td>" . htmlspecialchars($ausbildung_log['timestamp']) . "</td>";
                              echo "<td>" . htmlspecialchars($ausbildung_log['rating']) . "</td>";
                              echo "</tr>";
                          }
                          ?>
                        </tbody>
                        <tfoot>
                          <tr>
                            <th>id</th>
                            <th>user_id</th>
                            <th>editor_name</th>
                            <th>ausbildung</th>
                            <th>action</th>
                            <th>timestamp</th>
                            <th>rating</th>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>

                </div>
                  <div class="tab-pane fade" id="ausruestung-logs" role="tabpanel" aria-labelledby="ausruestung-logs-tab">

                  <div class="card">
                    <div class="card-body">
                      <table id="example1" class="table table-bordered table-striped">
                        <thead>
                          <tr>
                            <th>id</th>
                            <th>user_id</th>
                            <th>editor_name</th>
                            <th>key_name</th>
                            <th>action</th>
                            <th>timestamp</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          // Schleife durch die Logs und Ausgabe der Daten in der Tabelle
                          foreach ($ausruestung_logs as $ausruestung_log) {
                              echo "<tr>";
                              echo "<td>" . htmlspecialchars($ausruestung_log['id']) . "</td>";
                              echo "<td>" . htmlspecialchars($ausruestung_log['user_id']) . "</td>";
                              echo "<td>" . htmlspecialchars($ausruestung_log['editor_name']) . "</td>";
                              echo "<td>" . htmlspecialchars($ausruestung_log['key_name']) . "</td>";
                              echo "<td>" . htmlspecialchars($ausruestung_log['action']) . "</td>";
                              echo "<td>" . htmlspecialchars($ausruestung_log['timestamp']) . "</td>";
                              echo "</tr>";
                          }
                          ?>
                        </tbody>
                        <tfoot>
                          <tr>
                          <th>id</th>
                            <th>user_id</th>
                            <th>editor_name</th>
                            <th>key_name</th>
                            <th>action</th>
                            <th>timestamp</th>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>

                </div>
                  <div class="tab-pane fade" id="kunden-logs" role="tabpanel" aria-labelledby="kunden-logs-tab">

                  <div class="card">
                    <div class="card-body">
                      <table id="example1" class="table table-bordered table-striped">
                        <thead>
                          <tr>
                            <th>id</th>
                            <th>created_by</th>
                            <th>created_by_name</th>
                            <th>action</th>
                            <th>target_user</th>
                            <th>timestamp</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          // Schleife durch die Logs und Ausgabe der Daten in der Tabelle
                          foreach ($kunden_logs as $kunden_log) {
                              echo "<tr>";
                              echo "<td>" . htmlspecialchars($kunden_log['id']) . "</td>";
                              echo "<td>" . htmlspecialchars($kunden_log['created_by']) . "</td>";
                              echo "<td>" . htmlspecialchars($kunden_log['created_by_name']) . "</td>";
                              echo "<td>" . htmlspecialchars($kunden_log['action']) . "</td>";
                              echo "<td>" . htmlspecialchars($kunden_log['target_user']) . "</td>";
                              echo "<td>" . htmlspecialchars($kunden_log['timestamp']) . "</td>";
                              echo "</tr>";
                          }
                          ?>
                        </tbody>
                        <tfoot>
                          <tr>
                            <th>id</th>
                            <th>created_by</th>
                            <th>created_by_name</th>
                            <th>action</th>
                            <th>target_user</th>
                            <th>timestamp</th>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>

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
