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
            <h3 class="card-title">Responsive Hover Table</h3>

            <div class="card-tools">
              <div class="input-group input-group-sm" style="width: 150px;">
                <input type="text" name="table_search" class="form-control float-right" placeholder="Search">

                <div class="input-group-append">
                  <button type="submit" class="btn btn-default">
                    <i class="fas fa-search"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Rang</th>
                  <th>Ebene</th>
                  <th>Bearbeiten</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td>CEO</td>
                  <td>Inhaber</td>
                  <td>                              <button type="button" class="btn btn-block btn-outline-secondary" 
        data-toggle="modal" 
        data-target="#modal-default" 
        data-id="1">Bearbeiten</button>
                  </td>
                </tr>
                <tr>
                  <td>2</td>
                  <td>CFO</td>
                  <td>Geschäftsführung</td>
                  <td>              <button type="button" class="btn btn-block btn-outline-secondary">Bearbeiten</button>
                  </td>
                </tr>
                <tr>
                  <td>3</td>
                  <td>COO</td>
                  <td>Geschäftsführung</td>
                  <td>              <button type="button" class="btn btn-block btn-outline-secondary">Bearbeiten</button>
                  </td>
                </tr>
                <tr>
                  <td>4</td>
                  <td>CTO</td>
                  <td>Geschäftsführung</td>
                  <td>              <button type="button" class="btn btn-block btn-outline-secondary">Bearbeiten</button>
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
    <!-- /.card -->
  </div>
  <!-- /.col -->
</div>
<!-- /.row -->
</div>
<!-- /.container-fluid -->
</section>
    
    
<div class="modal fade" id="modal-default">
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
                    <label for="exampleInputEmail1">Rang</label>
                    <input class="form-control" type="Rang" placeholder="CEO">                  
                  </div>
                <div class="form-group">
                  <label for="exampleSelectBorder">Rang Ebene </label>
                  <select class="custom-select form-control-border" id="exampleSelectBorder">
                    <option>Inhaber</option>
                    <option>Geschäftsführung</option>
                    <option>Ausbildung</option>
                    <option>Mitarbeiter</option>
                  </select>
                </div>
                <label for="exampleInputEmail1">Mitarbeiter Bereich</label>
                  <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="exampleCheck1">
                    <label class="form-check-label" for="exampleCheck1">Recht 1</label>
                    <div>
                    <input type="checkbox" class="form-check-input" id="exampleCheck2">
                    <label class="form-check-label" for="exampleCheck2">Recht 2</label>
                    </div>
                  </div>
                  <label for="exampleInputEmail1">Leitungs Bereich</label>
                  <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="exampleCheck1">
                    <label class="form-check-label" for="exampleCheck1">Recht 1</label>
                    <div>
                    <input type="checkbox" class="form-check-input" id="exampleCheck2">
                    <label class="form-check-label" for="exampleCheck2">Recht 2</label>
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

      

    <script>$(document).on('click', '[data-target="#modal-default"]', function () {
    const roleId = $(this).data('id'); // ID des Rangs aus dem Button

    // AJAX-Anfrage, um die Rang-Daten abzurufen
    $.ajax({
        url: 'include/get_role.php', // PHP-Datei, die die Rangdaten liefert
        type: 'GET',
        data: { id: roleId },
        dataType: 'json',
        success: function (response) {
            // Fülle die Modal-Felder mit den Daten
            $('#modal-default input[placeholder="CEO"]').val(response.name);
            $('#modal-default select#exampleSelectBorder').val(response.level);
            
            // Rechte markieren
            $('#modal-default input[type="checkbox"]').each(function () {
                const checkbox = $(this);
                const right = checkbox.attr('id'); // Checkbox-ID entspricht dem Recht
                checkbox.prop('checked', response.permissions.includes(right));
            });
        },
        error: function () {
            alert('Fehler beim Laden der Rangdaten.');
        }
    });
});
</script>
<script> 
  $('#modal-default .btn-primary').click(function () {
    const roleId = $('[data-target="#modal-default"]').data('id'); // ID des Rangs
    const name = $('#modal-default input[placeholder="CEO"]').val();
    const level = $('#modal-default select#exampleSelectBorder').val();
    const permissions = [];

    // Alle markierten Rechte sammeln
    $('#modal-default input[type="checkbox"]:checked').each(function () {
        permissions.push($(this).attr('id')); // ID entspricht dem Recht
    });

    // AJAX-Anfrage, um die Änderungen zu speichern
    $.ajax({
        url: 'include/update_role.php', // PHP-Datei, die die Änderungen speichert
        type: 'POST',
        data: { id: roleId, name: name, level: level, permissions: JSON.stringify(permissions) },
        success: function (response) {
            if (response.success) {
                alert('Rang erfolgreich aktualisiert.');
                location.reload(); // Seite neu laden, um Änderungen anzuzeigen
            } else {
                alert('Fehler beim Speichern: ' + response.message);
            }
        },
        error: function () {
            alert('Fehler beim Speichern der Änderungen.');
        }
    });
});
</script>

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
