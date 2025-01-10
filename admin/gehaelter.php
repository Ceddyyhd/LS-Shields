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
            <h1 class="m-0">Gehälter</h1>
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
include 'include/db.php';

// SQL-Abfrage, um die Mitarbeiter-Daten (Name und Kontonummer) zu holen
$stmtEmployees = $conn->prepare("SELECT id, name, kontonummer FROM users WHERE bewerber = 'nein' AND gekuendigt = 'no_kuendigung'");
$stmtEmployees->execute();
$employees = $stmtEmployees->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
  <div class="card-header">
    <h3 class="card-title">Mitarbeiter Finanzen</h3>
    <div class="card-tools">
        <?php if ($_SESSION['permissions']['role_create'] ?? false): ?>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-neuen-eintrag">Neuen Eintrag hinzufügen</button>
        <?php endif; ?>
    </div>
  </div>
  <div class="card-body">
    <table id="example1" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>Mitarbeiter</th>
          <th>Kontonummer</th>
          <th>Gehalt</th>
          <th>Anteil</th>
          <th>Trinkgeld</th>
          <th>Löschen</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($employees as $employee): ?>
          <tr>
            <td><?php echo htmlspecialchars($employee['name']); ?></td>
            <td><?php echo htmlspecialchars($employee['kontonummer']); ?></td>
            <td>
              <?php 
                // Hier kann später der Gehaltseintrag dynamisch eingefügt werden
                echo '500$';
              ?>
            </td>
            <td>
              <?php 
                // Hier kann später der Anteilseintrag dynamisch eingefügt werden
                echo '500$';
              ?>
            </td>
            <td>
              <?php 
                // Hier kann später der Trinkgeldbetrag dynamisch eingefügt werden
                echo '500$';
              ?>
            </td>
            <td>
              <!-- Löschen Button für diesen Mitarbeiter -->
              <button class="btn btn-danger btn-sm delete-employee" data-userid="<?php echo $employee['id']; ?>">
                Auszahlung
              </button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>



<div class="modal fade" id="modal-neuen-eintrag">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Neuen Eintrag hinzufügen</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="addFinanceForm">
          <div class="card-body">

            <!-- Dropdown für Mitarbeiter -->
            <div class="form-group">
              <label for="employeeSelect">Mitarbeiter</label>
              <select id="employeeSelect" class="custom-select">
                <option value="">Bitte wählen</option>
                <!-- Mitarbeiter werden hier dynamisch geladen -->
              </select>
            </div>

            <!-- Dropdown für Art (Gehalt, Anteil, Trinkgeld) -->
            <div class="form-group">
              <label for="artSelect">Art</label>
              <select id="artSelect" class="custom-select">
                <option value="Gehalt">Gehalt</option>
                <option value="Anteil">Anteil</option>
                <option value="Trinkgeld">Trinkgeld</option>
              </select>
            </div>

            <!-- Eingabefeld für Betrag -->
            <div class="form-group">
              <label for="betragInput">Betrag</label>
              <input type="text" id="betragInput" class="form-control" placeholder="Betrag eingeben">
            </div>
            
          </div>
        </form>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
        <button type="button" class="btn btn-primary" id="saveFinanceButton">Speichern</button>
      </div>
    </div>
  </div>
</div>



<script>
    $(document).ready(function () {
    // Mitarbeiter Dropdown füllen
    employees.forEach(employee => {
        $('#employeeSelect').append(
            `<option value="${employee.id}">${employee.name} - ${employee.kontonummer}</option>`
        );
    });

    // Event-Listener für den Speichern-Button im Modal
    $('#saveFinanceButton').click(function () {
        const employeeId = $('#employeeSelect').val(); // Mitarbeiter
        const art = $('#artSelect').val(); // Art (Gehalt, Anteil, Trinkgeld)
        const betrag = parseFloat($('#betragInput').val()); // Betrag
        const notiz = 'Eingetragener Betrag für den Mitarbeiter'; // Hier kann eine Notiz hinzugefügt werden

        // Überprüfen, ob der Betrag eine gültige Zahl ist
        if (isNaN(betrag)) {
            alert('Bitte geben Sie einen gültigen Betrag ein!');
            return;
        }

        // Daten für die beiden Tabellen vorbereiten
        const historyData = {
            user_id: employeeId,
            betrag: betrag,
            art: art,
            notiz: notiz
        };

        const totalData = {
            user_id: employeeId,
            art: art,
            betrag: betrag
        };

        // AJAX-Request um sowohl die Historie als auch die Gesamtanzahl zu speichern
        $.ajax({
            url: 'include/save_finance_entry.php',
            method: 'POST',
            data: {
                historyData: JSON.stringify(historyData),
                totalData: JSON.stringify(totalData)
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    alert(response.message);  // Erfolgsmeldung
                    $('#modal-neuen-eintrag').modal('hide');  // Modal schließen
                    location.reload();  // Seite neu laden, um Änderungen anzuzeigen
                } else {
                    alert('Fehler: ' + response.message);  // Fehlermeldung
                }
            },
            error: function () {
                alert('Es gab einen Fehler bei der Anfrage.');
            }
        });
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
