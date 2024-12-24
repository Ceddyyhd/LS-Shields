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


<!-- Dein HTML-Modal -->
<div class="modal fade" id="modal-user-create">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Benutzer Erstellen</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
              <form id="createUserForm">
              <div class="card-body">
                  <div class="form-group">
                      <label for="name">Name</label>
                      <input type="text" class="form-control" id="name" name="name" placeholder="Enter name">
                  </div>
                  <div class="form-group">
                      <label for="email">Email Adresse</label>
                      <input type="email" class="form-control" id="email" name="email" placeholder="Enter email">
                  </div>
                  <div class="form-group">
                      <label for="password">Passwort</label>
                      <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                  </div>
                  <div class="form-group">
                      <label for="confirmPassword">Passwort Bestätigen</label>
                      <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Password">
                  </div>
                  <div class="form-group">
                      <label for="profileImageInput">Profilbild</label>
                      <div class="input-group">
                          <div class="custom-file">
                              <input type="file" class="custom-file-input" id="profileImageInput" name="profile_image" accept="image/*">
                              <label class="custom-file-label" for="profileImageInput">Choose file</label>
                          </div>
                      </div>
                      <img id="profileImagePreview" src="#" alt="Profilbild Vorschau" style="max-width: 100%; margin-top: 10px; display: none;">
                  </div>
              </div>
          </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Dein JavaScript -->
<script>
    document.getElementById('profileImageInput').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('profileImagePreview');
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            preview.src = '#';
            preview.style.display = 'none';
        }
    });

    // Listener nur für den "Save changes"-Button im Modal
    document.querySelector('.btn-primary').addEventListener('click', function() {
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();
    const confirmPassword = document.getElementById('confirmPassword').value.trim();

    // Prüfen, ob die Felder leer sind
    if (!email || !password) {
        alert('Bitte füllen Sie die erforderlichen Felder aus (Email, Passwort)!');
        return;
    }

    if (password !== confirmPassword) {
        alert('Die Passwörter stimmen nicht überein!');
        return;
    }

    // Formulardaten sammeln und absenden
    const formData = new FormData(document.getElementById('createUserForm'));
    fetch('user_create.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Benutzer erfolgreich erstellt.');
            location.reload(); // Seite neu laden, um Änderungen anzuzeigen
        } else {
            alert('Fehler: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Fehler:', error);
        alert('Ein unerwarteter Fehler ist aufgetreten.');
    });
});
</script>

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
