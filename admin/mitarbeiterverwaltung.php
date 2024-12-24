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
  <!-- /.content-header -->

  <!-- Main content -->
  <div class="card">
    <div class="card-header">
      <h3 class="card-title"><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-user-create">
                  Launch Primary Modal
                </button></h3>
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


<div class="modal fade" id="modal-user-create">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Default Modal</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
            <form>
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Email adresse</label>
                    <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Umail adresse</label>
                    <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Kontonummer</label>
                    <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Tel. Nr.</label>
                    <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1">Password</label>
                    <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputFile">Profilbild</label>
                    <div class="input-group">
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="exampleInputFile">
                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                      </div>
                      <div class="input-group-append">
                        <span class="input-group-text">Upload</span>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- /.card-body -->
              </form>
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

<!-- JavaScript Section -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.6/js/dataTables.bootstrap4.min.js"></script>

<script>
  $(document).ready(function () {
    $.ajax({
        url: 'https://ls-shields.ceddyyhd2.eu/admin/include/fetch_users.php',
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
                            <a href="/admin/profile.php?id=${user.id}" class="btn btn-block btn-outline-secondary">Bearbeiten</a>
                        </td>
                    </tr>
                `);
            });
        },
        error: function (xhr, status, error) {
            console.error('AJAX-Fehler:', xhr.responseText);
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
