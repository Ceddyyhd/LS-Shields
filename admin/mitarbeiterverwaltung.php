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
    include 'include/db.php';
    // Mitarbeiterdaten abrufen
$sql = "SELECT 
            u.id,
            u.name,
            u.nummer,
            u.created_at,
            r.name AS role_name
        FROM users u
        LEFT JOIN roles r ON u.role_id = r.id";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">DataTable with default features</h3>
    </div>
    <div class="card-body">
        <table id="example1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Mitarbeiter</th>
                    <th>Rang</th>
                    <th>Telefonnummer</th>
                    <th>Beitritt</th>
                    <th>Urlaub</th>
                    <th>Bearbeiten</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['role_name']) ?></td>
                        <td><?= htmlspecialchars($user['nummer'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars(date('d.m.Y', strtotime($user['created_at']))) ?></td>
                        <td>N/A</td>
                        <td>
                            <a href="/profile.php?id=<?= $user['id'] ?>" class="btn btn-block btn-outline-secondary">Bearbeiten</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>Mitarbeiter</th>
                    <th>Rang</th>
                    <th>Telefonnummer</th>
                    <th>Beitritt</th>
                    <th>Urlaub</th>
                    <th>Bearbeiten</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
    <!-- /.card -->
  </div>
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
