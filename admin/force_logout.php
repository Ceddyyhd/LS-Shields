<!DOCTYPE html>
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
            <h1 class="m-0">Benutzerverwaltung</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Benutzerverwaltung</li>
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
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-anfrage-create">
                Anfrage erstellen
            </button>
          </div>

          <div class="card-body">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>E-Mail</th>
                  <th>Aktionen</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // Alle Benutzer abfragen (nur Admins haben Zugriff auf diese Funktion)
                include 'include/db.php';

                // Beispielhafte Benutzerabfrage (mit einer Admin-Berechtigung für diesen Bereich)
                $query = "SELECT id, name, umail FROM kunden";
                $stmt = $conn->prepare($query);
                $stmt->execute();
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($users as $user): ?>
                  <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['umail']) ?></td>
                    <td>
                      <!-- Force-Logout Button -->
                      <button class="btn btn-danger" onclick="forceLogout(<?= $user['id'] ?>)">Zwangs-Logout</button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- JavaScript für Force-Logout -->
    <script>
      function forceLogout(userId) {
        if (confirm("Möchten Sie den Benutzer wirklich abmelden?")) {
          fetch('include/force_logout.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `user_id=${userId}`,
          })
            .then((response) => response.json())
            .then((data) => {
              if (data.success) {
                alert('Benutzer wurde erfolgreich abgemeldet.');
                location.reload();  // Seite neu laden, um die Änderungen zu sehen
              } else {
                alert('Fehler: ' + data.message);
              }
            })
            .catch((error) => {
              alert('Ein Fehler ist aufgetreten: ' + error.message);
            });
        }
      }
    </script>

  </div>
  <!-- /.content-wrapper -->

  <footer class="main-footer">
    <div class="float-right d-none d-sm-inline">
      Anything you want
    </div>
    <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
  </footer>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>
