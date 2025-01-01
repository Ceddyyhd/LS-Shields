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
$user_id = $_SESSION['user_id'] ?? 'Keine ID vorhanden';


$query = "SELECT * FROM kunden WHERE id = :kunde_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':kunde_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC); // fetch statt fetchAll, da nur ein Datensatz erwartet wird

?>


<div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Quick Example</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form>
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">UMail Adresse</label>
                    <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email" value="<?= htmlspecialchars($user['umail']); ?>">
                  </div>

                  <div class="form-group">
                    <label for="exampleInputPassword1">(Firmen) Name</label>
                    <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password" value="<?= htmlspecialchars($user['name']); ?>">
                  </div>
                  
                  <div class="form-group">
                    <label for="exampleInputPassword1">Nummer</label>
                    <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password" value="<?= htmlspecialchars($user['nummer']); ?>">
                  </div>
                  
                  <div class="form-group">
                    <label for="exampleInputPassword1">Kontonummer</label>
                    <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password" value="<?= htmlspecialchars($user['kontonummer']); ?>">
                  </div>
                  
                  <div class="form-group">
                    <label for="exampleInputPassword1">Passwort</label>
                    <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
                  </div>
                  
                  <div class="form-group">
                    <label for="exampleInputPassword1">Passwort Bestätigen</label>
                    <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
                  </div>

                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </form>
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
