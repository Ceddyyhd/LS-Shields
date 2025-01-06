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
    

    
    <div class="card-body-ankuendigung">
      <!-- Ankündigungen werden hier dynamisch eingefügt -->
  </div>


    <script>
     $(document).ready(function() {
    // Ankündigungen abrufen
    $.ajax({
        url: 'include/index_fetch_ankuendigung.php',  // Neue Datei für das Abrufen der Ankündigungen
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data && data.length > 0) {
                // Ankündigungen nach Priorität sortieren
                const sortedData = data.sort((a, b) => {
                    const priorityOrder = { 'high': 3, 'mid': 2, 'low': 1 };
                    return priorityOrder[b.prioritaet.toLowerCase()] - priorityOrder[a.prioritaet.toLowerCase()];
                });

                // Ankündigungen in das Callout einfügen
                const cardBody = $('.card-body-ankuendigung');  // Der Bereich, in dem die Ankündigungen angezeigt werden
                cardBody.empty();  // Alte Ankündigungen leeren

                sortedData.forEach(function(ankuendigung) {
                    // Bestimme die Callout-Klasse basierend auf der Priorität
                    let calloutClass = '';
                    switch (ankuendigung.prioritaet.toLowerCase()) {
                        case 'low':
                            calloutClass = 'callout-success'; // Erfolgreich
                            break;
                        case 'mid':
                            calloutClass = 'callout-warning'; // Mittel
                            break;
                        case 'high':
                            calloutClass = 'callout-danger'; // Gefährlich
                            break;
                        default:
                            calloutClass = 'callout-info'; // Standard
                            break;
                    }

                    // Füge das Callout-Div für jede Ankündigung hinzu
                    cardBody.append(`
                        <div class="callout ${calloutClass}">
                            <h5>${ankuendigung.display_name}</h5>
                            <p>${ankuendigung.description}</p>
                            <p>- ${ankuendigung.created_by}</p>  <!-- Der Ersteller der Ankündigung -->
                        </div>
                    `);
                });
            } else {
            }
        },
        error: function() {
        }
    });
});


<?php 
// Datenbankverbindung
include 'include/db.php';

try {
    // Verbindung zur Datenbank herstellen
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Anwesenheitsdaten abrufen und nach dem 'value' der Rolle sortieren
    $stmt = $conn->prepare("SELECT u.name, a.timestamp, r.value 
                            FROM attendance a
                            JOIN users u ON a.user_id = u.id
                            JOIN roles r ON u.role_id = r.id
                            WHERE a.status = 'present'
                            ORDER BY r.value DESC, a.timestamp DESC");  // Zuerst nach 'value' der Rolle sortieren, dann nach Timestamp

    $stmt->execute();

    // Alle Anwesenheitsdaten abrufen
    $attendanceData = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Fehler beim Abrufen der Daten: " . $e->getMessage();
}
?>

    </script>
      <!-- TABLE: LATEST ORDERS -->
      <div class="card" style="width: 25%">
    <div class="card-header">
        <h3 class="card-title">Anwesenheit</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body p-0">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Anwesend Seit</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($attendanceData)): ?>
                    <?php foreach ($attendanceData as $attendance): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($attendance['name']); ?></td>
                            <td><?php echo date('d.m.Y H:i', strtotime($attendance['timestamp'])); ?> Uhr</td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2">Keine Anwesenheit erfasst.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>


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
