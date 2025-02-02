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
            <h1 class="m-0">Finanzverwaltung</h1>
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
    <script>
  $(document).ready(function() {
    // AJAX-Anfrage zum Abrufen der Finanzdaten
    $.ajax({
        url: 'include/get_financial_data.php',  // Dein PHP-Skript zum Abrufen der Daten
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            console.log('Erhaltene Daten:', data);  // Überprüfe die empfangenen Daten

            // Überprüfen, ob die Werte korrekt sind
            if (data) {
                // Umwandeln der Werte in numerische Werte
                var einnahmen = parseFloat(data.einnahmen) || 0;
                var ausgaben = parseFloat(data.ausgaben) || 0;
                var kontostand = parseFloat(data.kontostand) || 0;

                // Aktualisieren der Info-Boxen mit den abgerufenen Werten
                $('#kontostand').text(kontostand.toFixed(2) + ' €');
                $('#einnahmen').text(einnahmen.toFixed(2) + ' €');
                $('#ausgaben').text(ausgaben.toFixed(2) + ' €');
            } else {
                alert('Fehler beim Abrufen der Daten.');
            }
        },
        error: function() {
            alert('Es gab einen Fehler bei der Anfrage.');
        }
    });
});
</script>

    <!-- Main content -->
    <div class="card-body">
      <div class="row">
      <div class="col-md-3 col-sm-6 col-12">
      <div class="info-box shadow-sm">
        <span class="info-box-icon bg-info"><i class="far fa-flag"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Kontostand</span>
          <span class="info-box-number" id="kontostand">$</span> <!-- Kontostand -->
        </div>
          </div>
        </div>

        <div class="col-md-3 col-sm-6 col-12">
      <div class="info-box shadow-sm">
        <span class="info-box-icon bg-success"><i class="far fa-flag"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Einnahmen</span>
          <span class="info-box-number" id="einnahmen">$</span> <!-- Einnahmen -->
        </div>
          </div>
        </div>

        <div class="col-md-3 col-sm-6 col-12">
      <div class="info-box shadow-sm">
        <span class="info-box-icon bg-danger"><i class="far fa-flag"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Ausgaben</span>
          <span class="info-box-number" id="ausgaben">$</span> <!-- Ausgaben -->
        </div>
          </div>
        </div>

        <div class="col-md-3 col-sm-6 col-12">
          <div class="info-box shadow-sm">
            <span class="info-box-icon bg-warning"><i class="far fa-flag"></i></span>
            <div class="info-box-content">
            <?php if ($_SESSION['permissions']['finanzverwaltung_add_entry'] ?? false): ?>
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#neuen-kassen-eintrag">
                Neuen Kassen Eintrag
              </button>
              <?php endif; ?>


              <!-- Modal -->
              <div class="modal fade" id="neuen-kassen-eintrag">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h4 class="modal-title">Neuen Kassen Eintrag</h4>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <form id="kassen-form">
                        <div class="form-group">
                          <label>Typ</label>
                          <select class="custom-select" name="typ">
                            <option value="Ausgabe">Ausgabe</option>
                            <option value="Einnahme">Einnahme</option>
                          </select>
                        </div>

                        <div class="form-group">
                          <label>Kategorie</label>
                          <select class="custom-select" name="kategorie" id="kategorie">
                            
                          </select>
                        </div>

                        <div class="form-group">
                          <label>Notiz</label>
                          <input type="text" class="form-control" name="notiz" placeholder="Enter ...">
                        </div>

                        <div class="form-group">
                          <label>Betrag</label>
                          <input type="text" class="form-control" name="betrag" placeholder="Enter ...">
                        </div>
                        <input type="hidden" name="erstellt_von" value="<?php echo htmlspecialchars($user_name); ?>">

                        <button type="submit" class="btn btn-primary">Speichern</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script>
    $(document).ready(function() {
    // AJAX-Anfrage zum Abrufen der Kategorien
    $.ajax({
        url: 'include/finanzen_get_categories.php',  // Das PHP-Skript zum Abrufen der Kategorien
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            console.log('Erhaltene Kategorien:', data);  // Überprüfen, ob die Daten korrekt zurückgegeben werden

            // Wenn Daten vorhanden sind
            if (data && data.length > 0) {
                // Kategorien in das Dropdown einfügen
                data.forEach(function(category) {
                    $('#kategorie').append('<option value="' + category.name + '">' + category.name + '</option>');
                });
            } else {
                $('#kategorie').append('<option value="">Keine Kategorien verfügbar</option>');
            }
        },
        error: function() {
            alert('Fehler beim Laden der Kategorien.');
        }
    });
});
</script>

    <!-- Table für Finanzdaten -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Finanzdaten</h3>
      </div>
      <div class="card-body">
        <table id="example1" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>Typ</th>
              <th>Kategorie</th>
              <th>Notiz</th>
              <th>Erstellt von</th>
              <th>Betrag</th>
            </tr>
          </thead>
          <tbody>
            <!-- Hier werden die Daten dynamisch mit PHP eingefügt -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
<script>
  $(document).ready(function() {
    // AJAX-Anfrage zum Abrufen der Finanzdaten
    $.ajax({
        url: 'include/get_financial_data_table.php',  // Dein PHP-Skript zum Abrufen der Daten
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            console.log('Erhaltene Daten:', data);  // Überprüfe die empfangenen Daten

            if (data && data.length > 0) {
                var tableBody = $('#example1 tbody'); // Ziel-Tabelle

                // Leere die Tabelle, bevor neue Daten hinzugefügt werden
                tableBody.empty();

                // Füge die Finanzdaten in die Tabelle ein
                data.forEach(function(entry) {
                    tableBody.append(
                        '<tr>' +
                            '<td>' + entry.typ + '</td>' +
                            '<td>' + entry.kategorie + '</td>' +
                            '<td>' + entry.notiz + '</td>' +
                            '<td>' + entry.erstellt_von + '</td>' +
                            '<td>' + parseFloat(entry.betrag).toFixed(2) + ' €</td>' +
                        '</tr>'
                    );
                });
            } else {
                alert('Keine Finanzdaten gefunden.');
            }
        },
        error: function() {
            alert('Es gab einen Fehler bei der Anfrage.');
        }
    });
});
</script>
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

<script>
  // AJAX Formularübermittlung
  $('#kassen-form').on('submit', function(e) {
    e.preventDefault();

    var formData = $(this).serialize();

    $.ajax({
      url: 'include/finanzen_add_entry.php', // Dein PHP-Skript
      method: 'POST',
      data: formData,
      dataType: 'json',
      success: function(response) {
        if (response.status == 'success') {
          alert(response.message);
          $('#neuen-kassen-eintrag').modal('hide');
          // Hier kannst du die Tabelle oder die Boxen neu laden
          location.reload(); // Zum Beispiel die Seite neu laden
        } else {
          alert('Fehler: ' + response.message);
        }
      },
      error: function() {
        alert('Es gab einen Fehler beim Speichern.');
      }
    });
  });
</script>
</body>
</html>
