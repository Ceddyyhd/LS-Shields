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
    <h3 class="card-title">Benutzereinstellungen</h3>
  </div>
  <!-- /.card-header -->
  <!-- form start -->
  <form id="settingsForm">
    <div class="card-body">
      <div class="form-group">
        <label for="exampleInputEmail1">UMail Adresse</label>
        <input type="email" class="form-control" id="exampleInputEmail1" name="umail" placeholder="Enter email" value="<?= htmlspecialchars($user['umail']); ?>" required>
      </div>

      <div class="form-group">
        <label for="exampleInputName">Firmen Name</label>
        <input type="text" class="form-control" id="exampleInputName" name="name" placeholder="Enter Name" value="<?= htmlspecialchars($user['name']); ?>" required>
      </div>

      <div class="form-group">
        <label for="exampleInputNummer">Nummer</label>
        <input type="text" class="form-control" id="exampleInputNummer" name="nummer" placeholder="Enter Nummer" value="<?= htmlspecialchars($user['nummer']); ?>" required>
      </div>

      <div class="form-group">
        <label for="exampleInputKontonummer">Kontonummer</label>
        <input type="text" class="form-control" id="exampleInputKontonummer" name="kontonummer" placeholder="Enter Kontonummer" value="<?= htmlspecialchars($user['kontonummer']); ?>" required>
      </div>

      <div class="form-group">
        <label for="exampleInputPassword">Passwort</label>
        <input type="password" class="form-control" id="exampleInputPassword" name="password" placeholder="Enter new password">
      </div>

      <div class="form-group">
        <label for="exampleInputPasswordConfirm">Passwort Bestätigen</label>
        <input type="password" class="form-control" id="exampleInputPasswordConfirm" name="password_confirm" placeholder="Confirm new password">
      </div>
    </div>
    <!-- /.card-body -->

    <div class="card-footer">
      <button type="submit" class="btn btn-primary">Änderungen speichern</button>
    </div>
  </form>
</div>

<!-- JavaScript (AJAX) -->
<script>
  document.getElementById('settingsForm').addEventListener('submit', function (e) {
    e.preventDefault(); // Verhindert das normale Absenden des Formulars

    const formData = new FormData(this); // Holt die Formulardaten

    // AJAX-Anfrage senden
    fetch('include/update_settings_ajax.php', {
      method: 'POST',
      body: formData,
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Daten wurden erfolgreich aktualisiert.');
      } else {
        alert('Fehler: ' + data.message);
      }
    })
    .catch(error => {
      console.error('Fehler:', error);
      alert('Es gab ein Problem beim Speichern der Daten.');
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
