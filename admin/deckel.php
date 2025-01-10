<!DOCTYPE html>
<?php 
$user_name = $_SESSION['username'] ?? 'Gast'; // Standardwert, falls keine Session gesetzt ist

?>
<html lang="en">
    <?php include 'include/header.php'; ?>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- DataTables -->
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<!-- Stelle sicher, dass jQuery vor deinem JavaScript-Code eingebunden ist -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <?php include 'include/navbar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Tank - Deckel</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Finanzverwaltung</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    
    <!-- Table für Finanzdaten -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Finanzdaten</h3>
      </div>
      <div class="card-body">
        <table id="example1" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>Typ</th>
              <th>Kategorie</th>
              <th>Notiz</th>
              <th>Erstellt von</th>
              <th>Betrag</th>
            </tr>
          </thead>
          <tbody>
            <!-- Hier werden die Daten dynamisch mit PHP eingefügt -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!-- Footer -->
  <footer class="main-footer">
    <strong>&copy; 2024 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
  </footer>
</div>

<!-- jQuery und andere Skripte -->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>

</body>
</html>
