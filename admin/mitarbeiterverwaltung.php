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
<style>
  @media (min-width: 576px) {
    .col-sm-6 {
        -ms-flex: 0 0 50%;
        flex: 0 0 100%;
        max-width: 100%;
    }
}
</style>
  <div class="row">
          <div class="col-12 col-sm-6">
            <div class="card card-primary card-tabs">
              <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill" href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home" aria-selected="true">Mitarbeiter Verwaltung</a>
                  </li>
                  <?php if (isset($_SESSION['permissions']['view_gekuendigte_mitarbeiter']) && $_SESSION['permissions']['view_gekuendigte_mitarbeiter']): ?>
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-one-profile-tab" data-toggle="pill" href="#custom-tabs-one-profile" role="tab" aria-controls="custom-tabs-one-profile" aria-selected="false">Gekündigte Mitarbeiter Verwaltung</a>
                  </li>
                  <?php endif; ?>
                  <?php if (isset($_SESSION['permissions']['view_bewerbung_mitarbeiter']) && $_SESSION['permissions']['view_bewerbung_mitarbeiter']): ?>
                  <li class="nav-item">
                    <a class="nav-link" id="mitarbeiter-bewerbung-tab" data-toggle="pill" href="#mitarbeiter-bewerbung" role="tab" aria-controls="mitarbeiter-bewerbung" aria-selected="false">Mitarbeiter Bewerbung</a>
                  </li>
                  <?php endif; ?>
                </ul>
              </div>
              <div class="card-body">
                <div class="tab-content" id="custom-tabs-one-tabContent">
                  <div class="tab-pane fade show active" id="custom-tabs-one-home" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab">
                  <div class="card">
  <?php if (isset($_SESSION['permissions']['user_create']) && $_SESSION['permissions']['user_create']): ?>
    <div class="card-header">
        <h3 class="card-title">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-user-create">
                Benutzer erstellen
            </button>
        </h3>
    </div>
<?php endif; ?>
    <div class="card-body">
      <table id="example1" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>Mitarbeiter</th>
            <th>Rang</th>
            <th>Telefonnummer</th>
            <th>Beitritt</th>
            <th>Urlaub</th>
            <?php if (isset($_SESSION['permissions']['edit_employee']) && $_SESSION['permissions']['edit_employee']): ?>
              <th>Bearbeiten</th>
           <?php endif; ?>
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
            <?php if (isset($_SESSION['permissions']['edit_employee']) && $_SESSION['permissions']['edit_employee']): ?>
              <th>Bearbeiten</th>
           <?php endif; ?>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
                  </div>
                  <div class="tab-pane fade" id="custom-tabs-one-profile" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab">
                  <div class="card">
        <div class="card-body">
          <table id="example2" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Mitarbeiter</th>
                <th>Rang</th>
                <th>Telefonnummer</th>
                <th>Beitritt</th>
                <th>Urlaub</th>
                <?php if (isset($_SESSION['permissions']['edit_employee']) && $_SESSION['permissions']['edit_employee']): ?>
              <th>Bearbeiten</th>
           <?php endif; ?>
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
                <?php if (isset($_SESSION['permissions']['edit_employee']) && $_SESSION['permissions']['edit_employee']): ?>
              <th>Bearbeiten</th>
           <?php endif; ?>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>

    <div class="tab-pane fade" id="mitarbeiter-bewerbung" role="tabpanel" aria-labelledby="mitarbeiter-bewerbung">
                  <div class="card">
        <div class="card-body">
        <?php if (isset($_SESSION['permissions']['bewerber_create']) && $_SESSION['permissions']['bewerber_create']): ?>
    <div class="card-header">
        <h3 class="card-title">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-user-create-bewerber">
                Bewerber erstellen
            </button>
        </h3>
    </div>
<?php endif; ?>
          <table id="example3" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Mitarbeiter</th>
                <th>Rang</th>
                <th>Telefonnummer</th>
                <th>Beitritt</th>
                <th>Urlaub</th>
                <?php if (isset($_SESSION['permissions']['edit_employee']) && $_SESSION['permissions']['edit_employee']): ?>
              <th>Bearbeiten</th>
           <?php endif; ?>
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
                <?php if (isset($_SESSION['permissions']['edit_employee']) && $_SESSION['permissions']['edit_employee']): ?>
              <th>Bearbeiten</th>
           <?php endif; ?>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>


    <script>
  $(document).ready(function () {
    $.ajax({
      url: 'https://ls-shields.ceddyyhd2.eu/admin/include/fetch_users_bewerbung.php', // URL zu deiner neuen fetch_users_gekündigt.php
      type: 'POST',
      dataType: 'json',
      success: function (data) {
        console.log(data);  // Überprüfe die Antwortstruktur

        if (!Array.isArray(data)) {
          console.error('Die Antwort ist kein Array:', data);
          alert('Fehler: Antwort ist kein Array.');
          return; // Verhindert das Fortfahren, wenn die Antwort nicht korrekt ist
        }

        let tableBody = $('#example3 tbody');
        tableBody.empty();

        data.forEach(user => {
          tableBody.append(`
            <tr>
              <td>${user.name}</td>
              <td>${user.role_name}</td>
              <td>${user.nummer ? user.nummer : 'N/A'}</td>
              <td>${new Date(user.created_at).toLocaleDateString()}</td>
              <td>${user.next_vacation ? user.next_vacation : 'Kein Urlaub geplant'}</td>
              <?php if (isset($_SESSION['permissions']['edit_employee']) && $_SESSION['permissions']['edit_employee']): ?>
                      <td>
                        <a href="/admin/profile.php?id=${user.id}" class="btn btn-block btn-outline-secondary">Bearbeiten</a>
                    </td>
                  <?php endif; ?>
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

<!-- Dein HTML-Modal -->
<div class="modal fade" id="modal-user-create-bewerber">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Bewerber Erstellen</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="createUserForm">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Adresse</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required>
                        </div>
                        <div class="form-group">
                            <label for="umail">Umail Adresse</label>
                            <input type="email" class="form-control" id="umail" name="umail" placeholder="Enter umail" required>
                        </div>
                        <div class="form-group">
                            <label for="kontonummer">Kontonummer</label>
                            <input type="text" class="form-control" id="kontonummer" name="kontonummer" placeholder="Enter kontonummer">
                        </div>
                        <div class="form-group">
                            <label for="nummer">Tel. Nr.</label>
                            <input type="text" class="form-control" id="nummer" name="nummer" placeholder="Enter nummer">
                        </div>
                        <!-- Admin Bereich (Wird auf 0 gesetzt) -->
                        <input type="hidden" name="admin_bereich" value="0">
                        <!-- Bewerber (Wird auf 'ja' gesetzt) -->
                        <input type="hidden" name="bewerber" value="ja">
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveUserBtn">Save changes</button>
            </div>
        </div>
    </div>
</div>
<script>
$('#saveUserBtn').on('click', function() {
    const formData = new FormData(document.getElementById('createUserForm'));

    // Sende das Formular per AJAX an das PHP-Skript
    fetch('include/create_user.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Benutzer erfolgreich erstellt!');
            $('#modal-user-create-bewerber').modal('hide'); // Modal schließen
            location.reload(); // Optional: Seite neu laden, um den neuen Benutzer zu sehen
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

    <!-- Dein JavaScript -->
    <script>
  $(document).ready(function () {
    $.ajax({
      url: 'https://ls-shields.ceddyyhd2.eu/admin/include/fetch_users_gekuendigt.php', // URL zu deiner neuen fetch_users_gekündigt.php
      type: 'POST',
      dataType: 'json',
      success: function (data) {
        console.log(data);  // Überprüfe die Antwortstruktur

        if (!Array.isArray(data)) {
          console.error('Die Antwort ist kein Array:', data);
          alert('Fehler: Antwort ist kein Array.');
          return; // Verhindert das Fortfahren, wenn die Antwort nicht korrekt ist
        }

        let tableBody = $('#example2 tbody');
        tableBody.empty();

        data.forEach(user => {
          tableBody.append(`
            <tr>
              <td>${user.name}</td>
              <td>${user.role_name}</td>
              <td>${user.nummer ? user.nummer : 'N/A'}</td>
              <td>${new Date(user.created_at).toLocaleDateString()}</td>
              <td>${user.next_vacation ? user.next_vacation : 'Kein Urlaub geplant'}</td>
              <?php if (isset($_SESSION['permissions']['edit_employee']) && $_SESSION['permissions']['edit_employee']): ?>
                      <td>
                        <a href="/admin/profile.php?id=${user.id}" class="btn btn-block btn-outline-secondary">Bearbeiten</a>
                    </td>
                  <?php endif; ?>
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
                  </div>
                </div>
              </div>
              <!-- /.card -->
            </div>
          </div>
        </div>

  <!-- Main content -->
  
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
                <form id="createUserForm1">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="nameCreate">Name</label>
                            <input type="text" class="form-control" id="nameCreate" name="name" placeholder="Enter name">
                        </div>
                        <div class="form-group">
                            <label for="emailCreate">Email Adresse</label>
                            <input type="email" class="form-control" id="emailCreate" name="email" placeholder="Enter email">
                        </div>
                        <div class="form-group">
                            <label for="umailCreate">Umail Adresse</label>
                            <input type="email" class="form-control" id="umailCreate" name="umail" placeholder="Enter umail">
                        </div>
                        <div class="form-group">
                            <label for="kontonummerCreate">Kontonummer</label>
                            <input type="text" class="form-control" id="kontonummerCreate" name="kontonummer" placeholder="Enter kontonummer">
                        </div>
                        <div class="form-group">
                            <label for="nummerCreate">Tel. Nr.</label>
                            <input type="text" class="form-control" id="nummerCreate" name="nummer" placeholder="Enter nummer">
                        </div>
                        <div class="form-group">
                            <label for="passwordCreate">Passwort</label>
                            <input type="password" class="form-control" id="passwordCreate" name="password" placeholder="Password">
                        </div>
                        <div class="form-group">
                            <label for="confirmPasswordCreate">Passwort Bestätigen</label>
                            <input type="password" class="form-control" id="confirmPasswordCreate" name="confirmPassword" placeholder="Password">
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

<script>
    // Profilbildvorschau
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

    // Event Listener für den Save-Button
    document.querySelector('#modal-user-create .btn-primary').addEventListener('click', function() {
        const formData = new FormData();
        formData.append('name', document.querySelector('#nameCreate').value.trim());
        formData.append('email', document.querySelector('#emailCreate').value.trim());
        formData.append('umail', document.querySelector('#umailCreate').value.trim());
        formData.append('kontonummer', document.querySelector('#kontonummerCreate').value.trim());
        formData.append('nummer', document.querySelector('#nummerCreate').value.trim());
        formData.append('password', document.querySelector('#passwordCreate').value.trim());
        formData.append('confirmPassword', document.querySelector('#confirmPasswordCreate').value.trim());

        const profileImageInput = document.querySelector('#profileImageInput');
        if (profileImageInput.files[0]) {
            formData.append('profile_image', profileImageInput.files[0]);
        }

        // Überprüfung der Pflichtfelder
        const email = document.querySelector('#emailCreate').value.trim();
        const password = document.querySelector('#passwordCreate').value.trim();
        const confirmPassword = document.querySelector('#confirmPasswordCreate').value.trim();

        if (!email || !password) {
            alert('Bitte füllen Sie die erforderlichen Felder aus (Email, Passwort)!');
            return;
        }

        if (password !== confirmPassword) {
            alert('Die Passwörter stimmen nicht überein!');
            return;
        }

        // Debugging: Log FormData
        console.log('FormData:', formData);

        // Absenden der Formulardaten per Fetch
        fetch('include/user_create.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Benutzer erfolgreich erstellt.');
                location.reload(); // Seite neu laden
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
    url: 'include/fetch_users.php',
    type: 'POST',
    dataType: 'json',
    success: function (data) {
        console.log(data);  // Füge dies hinzu, um die Struktur von 'data' zu überprüfen

        // Wenn 'data' kein Array ist, handle den Fehler
        if (!Array.isArray(data)) {
            console.error('Die Antwort ist kein Array:', data);
            alert('Fehler: Antwort ist kein Array.');
            return; // Verhindert das Fortfahren, wenn die Antwort nicht korrekt ist
        }

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
                    <?php if (isset($_SESSION['permissions']['edit_employee']) && $_SESSION['permissions']['edit_employee']): ?>
                      <td>
                        <a href="/admin/profile.php?id=${user.id}" class="btn btn-block btn-outline-secondary">Bearbeiten</a>
                    </td>
                  <?php endif; ?>
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
