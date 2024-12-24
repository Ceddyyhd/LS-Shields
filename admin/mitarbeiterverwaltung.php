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
  <!-- /.content-header -->

  <!-- Main content -->
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
          <!-- Daten werden dynamisch geladen -->
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
</div>

<!-- JavaScript Section -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.6/js/dataTables.bootstrap4.min.js"></script>

<script>
  $(document).ready(function () {
    // Initialize DataTable
    $('#example1').DataTable();

    // Dynamisch Daten laden
    $.ajax({
    url: 'fetch_users.php',
    type: 'POST',
    dataType: 'json',
    success: function (data) {
        let tableBody = $('#example1 tbody');
        tableBody.empty();

        data.forEach(user => {
            tableBody.append(`
                <tr>
                    <td>${user.name}</td>
                    <td>${user.role_name}</td>
                    <td>${user.nummer ? user.nummer : 'N/A'}</td>
                    <td>${new Date(user.created_at).toLocaleDateString()}</td>
                    <td>${user.next_vacation ? user.next_vacation : 'Kein Urlaub geplant'}</td>
                    <td>
                        <a href="/profile.php?id=${user.id}" class="btn btn-block btn-outline-secondary">Bearbeiten</a>
                    </td>
                </tr>
            `);
        });
    },
    error: function () {
        alert('Fehler beim Abrufen der Daten.');
    }
}); 
  });
</script>

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
