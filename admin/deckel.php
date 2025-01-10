<!DOCTYPE html>
<?php 
$user_name = $_SESSION['username'] ?? 'Gast'; // Standardwert, falls keine Session gesetzt ist

?>
<html lang="en">
    <?php include 'include/header.php'; ?>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- DataTables -->
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<!-- Stelle sicher, dass jQuery vor deinem JavaScript-Code eingebunden ist -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <?php include 'include/navbar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Tank - Deckel</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Finanzverwaltung</li>
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

// SQL-Abfrage, um die Daten aus der Tabelle deckel zu holen und nach location zu gruppieren
$query = "
    SELECT 
    IFNULL(NULLIF(location, ''), 'Unbekannt') AS location,
    SUM(betrag) AS total_betrag
FROM deckel
GROUP BY location
";
$stmt = $conn->prepare($query);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
  <div class="card-header">
    <h3 class="card-title">Finanzdaten</h3>
  </div>
  <div class="card-body">
    <table id="example1" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>Location</th>
          <th>Betrag</th>
          <th>Löschen</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($results as $row): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['location']); ?></td>
            <td><?php echo number_format($row['total_betrag'], 2, ',', '.'); ?></td>
            <td>
              <!-- Löschen Button für diese Location -->
              <button class="btn btn-danger btn-sm delete-location" data-location="<?php echo htmlspecialchars($row['location']); ?>">
                Löschen
              </button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
  $(document).ready(function() {
  // Event-Listener für Löschen-Button
  $('.delete-location').click(function() {
    const location = $(this).data('location');
    
    if (confirm('Möchten Sie wirklich alle Einträge für die Location "' + location + '" löschen?')) {
      $.ajax({
        url: 'include/delete_location.php',  // Dein PHP-Skript zum Löschen der Location
        method: 'POST',
        data: { location: location },  // Sende die Location per POST
        dataType: 'json',  // Wir erwarten eine JSON-Antwort
        success: function(response) {
          if (response.success) {
            alert('Alle Einträge für die Location "' + location + '" wurden gelöscht.');
            // Zeile aus der Tabelle entfernen
            $(`[data-location="${location}"]`).closest('tr').remove();
          } else {
            alert('Fehler beim Löschen der Daten: ' + response.message);
          }
        },
        error: function() {
          alert('Es gab einen Fehler bei der Anfrage.');
        }
      });
    }
  });
});
</script>
  </div>
  <!-- Footer -->
  <footer class="main-footer">
    <strong>&copy; 2024 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
  </footer>
</div>

<!-- jQuery und andere Skripte -->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>

</body>
</html>
