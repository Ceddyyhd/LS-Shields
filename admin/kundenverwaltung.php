<!DOCTYPE html>
<html lang="en">
<?php include 'include/header.php'; ?>

<body class="hold-transition sidebar-mini">
<div class="wrapper">

<?php include 'include/navbar.php'; ?>

<!-- jQuery (notwendig für Bootstrap) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Kundenverwaltung</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Kundenverwaltung</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <?php if (isset($_SESSION['permissions']['customer_create']) && $_SESSION['permissions']['customer_create']): ?>
    <div class="card-header">
        <h3 class="card-title">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-kunde-create">
                Kunden erstellen
            </button>
        </h3>
    </div>
    <?php endif; ?>
    <div class="card-body">
      <table id="example1" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>Kundenname</th>
            <th>E-Mail</th>
            <th>Telefonnummer</th>
            <th>Beitritt</th>
            <th>Geloescht</th>
            <th>Aktionen</th>
          </tr>
        </thead>
        <tbody id="kunden-table-body">
          <!-- Daten werden dynamisch geladen -->
        </tbody>
        <tfoot>
          <tr>
            <th>Kundenname</th>
            <th>E-Mail</th>
            <th>Telefonnummer</th>
            <th>Beitritt</th>
            <th>Geloescht</th>
            <th>Aktionen</th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>

<!-- Kunden Erstellen Modal -->
<div class="modal fade" id="modal-kunde-create">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Kunden Erstellen</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="createCustomerForm">
                    <div class="form-group">
                        <label for="umail">E-Mail</label>
                        <input type="email" class="form-control" id="umail" name="umail" required>
                    </div>
                    <div class="form-group">
                        <label for="name">Kundenname</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="nummer">Telefonnummer</label>
                        <input type="text" class="form-control" id="nummer" name="nummer">
                    </div>
                    <div class="form-group">
                        <label for="kontonummer">Kontonummer</label>
                        <input type="text" class="form-control" id="kontonummer" name="kontonummer">
                    </div>
                    <button type="submit" class="btn btn-primary">Speichern</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Funktion zum Laden der Kunden
function loadCustomers() {
    $.ajax({
        url: 'include/fetch_kunden.php',
        type: 'POST',
        dataType: 'json',
        success: function(data) {
            let tableBody = $('#kunden-table-body');
            tableBody.empty();
            data.forEach(kunde => {
                tableBody.append(`
                    <tr>
                        <td>${kunde.name}</td>
                        <td>${kunde.umail}</td>
                        <td>${kunde.nummer ? kunde.nummer : 'N/A'}</td>
                        <td>${new Date(kunde.created_at).toLocaleDateString()}</td>
                        <td>${kunde.geloescht}</td>
                        <td>
                            <a href="kunden.php?id=${kunde.id}" class="btn btn-warning">Bearbeiten</a>
                        </td>
                    </tr>
                `);
            });
        },
        error: function(xhr, status, error) {
            alert('Fehler beim Laden der Kunden.');
        }
    });
}

// Formular zur Erstellung eines neuen Kunden
$('#createCustomerForm').submit(function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);

    fetch('include/kunden_create.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Kunde erfolgreich erstellt!');
            $('#modal-kunde-create').modal('hide');
            loadCustomers();  // Kunden neu laden
        } else {
            alert('Fehler: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Fehler:', error);
        alert('Ein unerwarteter Fehler ist aufgetreten.');
    });
});

// Beim Laden der Seite Kunden laden
$(document).ready(function() {
    loadCustomers();
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
