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
    
    <?php
// Datenbankverbindung einbinden
include 'include/db.php';

// Anfragen aus der Datenbank abrufen
$query = "SELECT id, vorname_nachname, telefonnummer, anfrage, datum_uhrzeit, status FROM anfragen ORDER BY datum_uhrzeit DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$anfragen = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Anfragen Tabelle</h3>
      </div>
      <!-- ./card-header -->
      <div class="card-body">
        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>#</th>
              <th>Ansprechpartner</th>
              <th>Anfrage</th>
              <th>Status</th>
              <th>Details</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($anfragen as $anfrage): ?>
              <tr data-widget="expandable-table" aria-expanded="false">
                <td><?= htmlspecialchars($anfrage['id']) ?></td>
                <td><?= htmlspecialchars($anfrage['vorname_nachname']) ?></td>
                <td><?= htmlspecialchars($anfrage['anfrage']) ?></td>
                <td><?= htmlspecialchars($anfrage['status']) ?></td>
                <td>Details einblenden</td>
              </tr>
              <tr class="expandable-body">
                <td colspan="5">
                  <div class="p-3">
                    <div class="mb-3">
                      <strong>Datum & Uhrzeit:</strong>
                      <div><?= htmlspecialchars($anfrage['datum_uhrzeit']) ?></div>
                    </div>
                    <div class="mb-3">
                      <strong>Telefonnummer:</strong>
                      <div><?= htmlspecialchars($anfrage['telefonnummer']) ?></div>
                    </div>
                    <div class="mb-3">
                      <strong>Status:</strong>
                      <div><?= htmlspecialchars($anfrage['status']) ?></div>
                    </div>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
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
