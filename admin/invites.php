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
    <div class="card">
    <div class="card-header">
    <?php if ($_SESSION['permissions']['add_vehicle'] ?? false): ?>
    <button type="button" class="btn btn-primary" id="generateInviteCodeBtn">
        Einladungscode hinzufügen
    </button>
<?php endif ?>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th style="width: 10px">#</th>
                    <th>invite_code</th>
                    <th>created_at</th>
                    <th>expired_at</th>
                </tr>
            </thead>
            <?php
// Stellen Sie sicher, dass eine gültige Verbindung zur Datenbank besteht
include 'include/db.php';

// Abfrage zum Abrufen der Einladungs-Codes
$stmt = $conn->prepare("SELECT * FROM invites ORDER BY created_at DESC");
$stmt->execute();
$invites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<tbody>
    <?php foreach ($invites as $invite): ?>
        <tr>
            <td><?php echo $invite['id']; ?></td>
            <td><?php echo htmlspecialchars($invite['invite_code']); ?></td>
            <td><?php echo $invite['created_at']; ?></td>
            <td><?php echo $invite['expired_at'] ?: 'Kein Ablaufdatum'; ?></td>
        </tr>
    <?php endforeach; ?>
</tbody>
        </table>
    </div>
</div>
    </div>
  </div>
<script>
    $(document).ready(function () {
    $('#generateInviteCodeBtn').click(function () {
        $.ajax({
            url: 'include/generate_invite_code.php',  // PHP-Skript zum Generieren des Einladungscodes
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    // Den neuen Code in der Tabelle oder irgendwo auf der Seite anzeigen
                    alert('Neuer Einladungscode: ' + response.invite_code);
                    
                    // Optional: Den neuen Code in die Tabelle einfügen
                    const newRow = `
                        <tr>
                            <td>${response.id}</td>
                            <td>${response.invite_code}</td>
                            <td>${response.created_at}</td>
                            <td>${response.expired_at || 'Kein Ablaufdatum'}</td>
                        </tr>
                    `;
                    $('#inviteTableBody').prepend(newRow);  // Den neuen Code an den Anfang der Tabelle einfügen
                } else {
                    alert('Fehler beim Erstellen des Einladungscodes: ' + response.message);
                }
            },
            error: function (xhr, status, error) {
                alert('Fehler beim Erstellen des Einladungscodes.');
            }
        });
    });
});

</script>
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
