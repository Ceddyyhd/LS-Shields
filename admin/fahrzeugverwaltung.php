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
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#vehicle-create">
        Fahrzeug Hinzufügen
    </button>
<?php else: ?>
    <!-- Optional: Eine Nachricht anzeigen, dass der Benutzer keine Berechtigung hat -->
    <h3 class="card-title">Fahrzeuge</h3>
    <?php endif; ?>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th style="width: 10px">#</th>
                    <th>Modell</th>
                    <th>Kennzeichen</th>
                    <th>Standort</th>
                    <th>Nächste Inspektion</th>
                    <th>Button</th>
                    <th>Tanken</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include 'include/db.php'; // Datenbankverbindung einbinden
                $limit = 25;
                $page = isset($_GET['page']) ? $_GET['page'] : 1;
                $offset = ($page - 1) * $limit;

                // Nur Fahrzeuge mit decommissioned = 0
                $sql = "SELECT * FROM vehicles WHERE decommissioned = 0 LIMIT $limit OFFSET $offset";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $vehicles = $stmt->fetchAll();

                foreach ($vehicles as $vehicle) {
                    // Berechne das Datum der letzten Inspektion
                    $inspection_date = strtotime($vehicle['next_inspection']);
                    $current_date = strtotime(date('Y-m-d'));
                    $two_weeks_later = strtotime('+2 weeks', $current_date);

                    // Bestimme den Badge-Typ je nach Inspektionsdatum
                    if ($inspection_date <= $current_date) {
                        $badge_class = 'bg-danger'; // Gefahr (Vergangen oder Heute)
                    } elseif ($inspection_date <= $two_weeks_later) {
                        $badge_class = 'bg-warning'; // Warnung (In den nächsten 2 Wochen)
                    } else {
                        $badge_class = 'bg-success'; // Erfolg (Mehr als 2 Wochen)
                    }

                    // Gebe die Fahrzeugdaten aus
                    echo '<tr>';
                    echo '<td>' . $vehicle['id'] . '</td>';
                    echo '<td>' . $vehicle['model'] . '</td>';
                    echo '<td>' . $vehicle['license_plate'] . '</td>';
                    echo '<td>' . $vehicle['location'] . '</td>';
                    echo '<td><span class="badge ' . $badge_class . '">' . date('d.m.Y', $inspection_date) . '</span></td>';
                    echo '<td><button type="button" class="btn btn-primary bearbeiten-button" data-toggle="modal" data-target="#vehicle-bearbeiten" data-vehicle-id="' . $vehicle['id'] . '">Fahrzeug Bearbeiten</button></td>';
                    echo '<td><button type="button" class="btn btn-primary tanken-button" data-toggle="modal" data-target="#vehicle-tanken" data-vehicle-id="' . $vehicle['id'] . '">Tanken</button></td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<!-- Modal for Editing Vehicle -->
<div class="modal fade" id="vehicle-bearbeiten">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Fahrzeug Bearbeiten</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editVehicleForm">
                    <div class="form-group">
                        <label>Modell</label>
                        <input type="text" class="form-control" name="model" id="edit-model" placeholder="Enter ..."
                            <?php if (!($_SESSION['permissions']['edit_model'] ?? false)) echo 'disabled'; ?>>
                    </div>

                    <div class="form-group">
                        <label>Kennzeichen</label>
                        <input type="text" class="form-control" name="license_plate" id="edit-license_plate" placeholder="Enter ..."
                            <?php if (!($_SESSION['permissions']['edit_license_plate'] ?? false)) echo 'disabled'; ?>>
                    </div>

                    <div class="form-group">
                        <label>Standort</label>
                        <input type="text" class="form-control" name="location" id="edit-location" placeholder="Enter ..."
                            <?php if (!($_SESSION['permissions']['edit_location'] ?? false)) echo 'disabled'; ?>>
                    </div>

                    <div class="form-group">
                        <label>Nächste Inspektion</label>
                        <input type="date" class="form-control" name="next_inspection" id="edit-next_inspection" placeholder="Enter ..."
                            <?php if (!($_SESSION['permissions']['edit_next_inspection'] ?? false)) echo 'disabled'; ?>>
                    </div>

                    <!-- Notizen-Feld -->
                    <div class="form-group">
                        <label for="edit-notes">Notizen</label>
                        <textarea class="form-control" id="edit-notes" name="notes" placeholder="Notizen..."
                            <?php if (!($_SESSION['permissions']['edit_notes'] ?? false)) echo 'disabled'; ?>></textarea>
                    </div>

                    <!-- Ausgemustert-Checkbox -->
                    <div class="form-group">
                        <strong><i class="fas fa-trash-alt mr-1"></i> Ausgemustert</strong>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="edit-decommissioned" name="decommissioned"
                                <?php if (!($_SESSION['permissions']['edit_decommissioned'] ?? false)) echo 'disabled'; ?>>
                            <label for="edit-decommissioned" class="form-check-label">Ausgemustert</label>
                        </div>
                    </div>

                    <input type="hidden" name="vehicle_id" id="edit-vehicle_id">

                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                    <input type="hidden" name="user_name" value="<?php echo $_SESSION['username']; ?>">
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Tanken -->
<!-- Modal for Tanken -->
<div class="modal fade" id="vehicle-tanken">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Fahrzeug Tanken</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="tankenForm"> <!-- Spezielles Formular für Tanken -->
                    <div class="form-group">
                        <label>Kennzeichen</label>
                        <input type="text" class="form-control" name="license_plate" id="edit-license_plate" placeholder="Kennzeichen">
                        </div>

                    <div class="form-group">
                        <strong><i class="fas fa-gas-pump mr-1"></i> Tanken</strong>
                        <div class="form-check">
                            <input type="checkbox" id="edit-fuel-checkbox" class="form-check-input" name="fuel_checked">
                            <label for="edit-fuel-checkbox" class="form-check-label">Getankt</label>
                        </div>
                        <input type="text" id="edit-fuel-location" name="fuel_location" class="form-control" placeholder="Wo wurde getankt?">
                        <input type="number" id="edit-fuel-amount" name="fuel_amount" class="form-control mt-2" placeholder="Betrag in EUR">
                    </div>

                    <input type="hidden" name="vehicle_id" id="edit-vehicle_id">
                    <input type="hidden" name="user_name" value="<?php echo $_SESSION['username']; ?>">

                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



      <!-- Modal for Adding Vehicle -->
      <div class="modal fade" id="vehicle-create">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Fahrzeug Hinzufügen</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <form id="addVehicleForm">
                    <div class="form-group">
                        <label>Modell</label>
                        <input type="text" class="form-control" name="model" placeholder="Enter ...">
                    </div>

                    <div class="form-group">
                        <label>Kennzeichen</label>
                        <input type="text" class="form-control" name="license_plate" placeholder="Enter ...">
                    </div>

                    <div class="form-group">
                        <label>Standort</label>
                        <input type="text" class="form-control" name="location" placeholder="Enter ...">
                    </div>

                    <div class="form-group">
                        <label>Nächste Inspektion</label>
                        <input type="date" class="form-control" name="next_inspection" placeholder="Enter ...">
                    </div>

                    <div class="modal-footer justify-content-between">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                    <input type="hidden" name="user_name" value="<?php echo $_SESSION['username']; ?>">
                </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Log Table -->
      <div class="card">
    <div class="card-header">
        <h3 class="card-title">Fahrzeuge Logs</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th style="width: 10px">#</th>
                    <th>Task</th>
                    <th>Progress</th>
                    <th style="width: 40px">Label</th>
                </tr>
            </thead>
            <tbody id="logsTable">
                <!-- Die Logs werden hier durch JavaScript eingefügt -->
            </tbody>
        </table>
    </div>
    <div class="card-footer clearfix">
    <ul class="pagination pagination-sm m-0 float-right">
        <!-- Pagination Links werden hier durch JavaScript eingefügt -->
    </ul>
</div>
</div>

    </div>
  </div>

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

<!-- AJAX-Skripte -->
<script>
$(document).ready(function() {
    // Fahrzeug Hinzufügen (AJAX)
    $('#addVehicleForm').on('submit', function(e) {
    e.preventDefault();
    var formData = $(this).serialize();
    
    $.ajax({
        url: 'include/vehicle_create.php',
        method: 'POST',
        data: formData,
        dataType: 'json',  // Füge dies hinzu, um die Antwort als JSON zu behandeln
        success: function(response) {
            console.log(response);  // Ausgabe der Antwort zur Überprüfung
            if (response.success) {
                alert('Fahrzeug erfolgreich hinzugefügt');
                location.reload();
            } else {
                alert('Fehler beim Hinzufügen des Fahrzeugs: ' + response.message);
            }
        },
        error: function() {
            alert('Ein Fehler ist aufgetreten');
        }
    });
});

$('.tanken-button').on('click', function() {
    var vehicleId = $(this).data('vehicle-id');  // Die Fahrzeug-ID aus dem Button-Attribut holen

    // Fahrzeugdaten laden
    $.ajax({
        url: 'include/vehicle_fetch.php',  // Deine PHP-Datei, die Fahrzeugdaten abruft
        method: 'GET',
        data: { vehicle_id: vehicleId },  // Die Fahrzeug-ID an die PHP-Datei übergeben
        success: function(response) {
            console.log(response);  // Ausgabe der Antwort zur Überprüfung

            try {
                var vehicle = JSON.parse(response);  // Antwort als JSON parsen
                $('#edit-license_plate').val(vehicle.license_plate);  // Kennzeichen im Tanken Modal

                // Debugging: Zeige den Wert im Alert
                alert("Kennzeichen: " + vehicle.license_plate);

                $('#edit-vehicle_id').val(vehicle.id);  // Fahrzeug-ID (für das versteckte Feld)

                // Zeige das Tanken-Modal
                $('#vehicle-tanken').modal('show');
            } catch (e) {
                alert('Fehler: Ungültige JSON-Antwort');
                console.error('JSON Parsing Error: ', e);
            }
        },
        error: function() {
            alert('Fehler beim Laden der Fahrzeugdaten');
        }
    });
});


    // Fahrzeug Bearbeiten (AJAX)
    $('.bearbeiten-button').on('click', function() {
    var vehicleId = $(this).data('vehicle-id');
    
    // Fahrzeugdaten laden
    $.ajax({
        url: 'include/vehicle_fetch.php',
        method: 'GET',
        data: { vehicle_id: vehicleId },
        success: function(response) {
            var vehicle = JSON.parse(response);
            
            // Werte in die Formularfelder einfügen
            $('#edit-model').val(vehicle.model);
            $('#edit-license_plate').val(vehicle.license_plate);
            $('#edit-location').val(vehicle.location);
            $('#edit-next_inspection').val(vehicle.next_inspection);
            $('#edit-vehicle_id').val(vehicle.id);

            // Notizen und Ausgemustert-Status einfügen
            $('#edit-notes').val(vehicle.notes);  // Notizen
            $('#edit-decommissioned').prop('checked', vehicle.decommissioned == 1);  // Ausgemustert
        }
    });
});

    // Fahrzeug Bearbeiten speichern (AJAX)
    $('#editVehicleForm').on('submit', function(e) {
    e.preventDefault();
    var formData = $(this).serialize();

    $.ajax({
        url: 'include/vehicle_update.php',  // URL zum PHP-Skript
        method: 'POST',
        data: formData,
        dataType: 'json',  // Sicherstellen, dass die Antwort als JSON interpretiert wird
        success: function(response) {
            console.log(response);  // Ausgabe der Antwort zur Überprüfung
            if (response.success) {
                alert('Fahrzeug erfolgreich bearbeitet');
                location.reload();  // Seite neu laden oder eine andere Aktion durchführen
            } else {
                alert('Fehler beim Bearbeiten des Fahrzeugs: ' + response.message);  // Zeigt die Fehlermeldung an
            }
        },
        error: function(xhr, status, error) {
            // Fehlerbehandlung für den AJAX-Request
            alert('Ein Fehler ist aufgetreten: ' + error);
        }
        });
    });
    $(document).ready(function() {
    var currentPage = 1;  // Startseite
    var totalPages = 1;   // Gesamtseitenzahl

    // Logs per AJAX abrufen
    function loadVehicleLogs(page) {
        $.ajax({
            url: 'include/vehicle_logs_fetch.php',  // PHP-Datei, die Logs und Seitenanzahl zurückgibt
            method: 'GET',
            data: { page: page },  // Die aktuelle Seite übergeben
            dataType: 'json',
            success: function(response) {
                if (response.success === false) {
                    alert('Fehler beim Abrufen der Logs: ' + response.message);
                    return;
                }

                var logsTable = $('#logsTable');  // Die Tabelle im HTML, wo die Logs eingefügt werden
                logsTable.empty();  // Leere die Tabelle, bevor neue Daten eingefügt werden

                // Logs in die Tabelle einfügen
                response.logs.forEach(function(log) {
                    var logRow = '<tr>';
                    logRow += '<td>' + log.id + '</td>';
                    logRow += '<td>' + log.action + '</td>';
                    logRow += '<td>' + log.timestamp + '</td>';
                    logRow += '<td>' + log.user_name + '</td>';
                    logRow += '</tr>';
                    logsTable.append(logRow);
                });

                // Pagination anzeigen
                totalPages = response.total_pages;
                updatePagination();
            },
            error: function() {
                alert('Fehler beim Abrufen der Logs');
            }
        });
    }

    $(document).ready(function() {
    // Beim Absenden des Tanken-Formulars
    $('#tankenForm').submit(function(event) {
    event.preventDefault();  // Verhindert das Standard-Formular-Absenden

    // Formulardaten sammeln
    const formData = $(this).serialize();  // Sammelt die Daten des Formulars

    // AJAX-Anfrage an vehicle_tanken.php
    $.ajax({
        url: 'include/vehicle_tanken.php',  // Deine PHP-Datei für das Tanken
        type: 'POST',
        data: formData,
        dataType: 'json',  // Erwartet eine JSON-Antwort
        success: function(response) {
            if (response.success) {
                alert('Fahrzeugdaten und Tanken-Daten erfolgreich aktualisiert.');
                $('#vehicle-tanken').modal('hide');  // Modal schließen
                location.reload();  // Seite neu laden, um die Änderungen zu sehen
            } else {
                alert('Fehler: ' + response.message);
            }
        },
        error: function() {
            alert('Fehler beim Absenden der Anfrage.');
        }
        });
    });
});


    // Pagination-Links aktualisieren
    function updatePagination() {
        var pagination = $('.pagination');
        pagination.empty();  // Leere die Pagination vor dem Hinzufügen neuer Links

        // „Zurück“-Link
        pagination.append('<li class="page-item ' + (currentPage == 1 ? 'disabled' : '') + '"><a class="page-link" href="#" onclick="changePage(' + (currentPage - 1) + ')">&laquo;</a></li>');

        // Seiten-Links
        for (var i = 1; i <= totalPages; i++) {
            pagination.append('<li class="page-item ' + (i == currentPage ? 'active' : '') + '"><a class="page-link" href="#" onclick="changePage(' + i + ')">' + i + '</a></li>');
        }

        // „Weiter“-Link
        pagination.append('<li class="page-item ' + (currentPage == totalPages ? 'disabled' : '') + '"><a class="page-link" href="#" onclick="changePage(' + (currentPage + 1) + ')">&raquo;</a></li>');
    }

    // Funktion zum Seitenwechsel
    window.changePage = function(page) {
        if (page < 1 || page > totalPages) return;  // Seiten außerhalb des Bereichs vermeiden
        currentPage = page;
        loadVehicleLogs(currentPage);
    };

    // Lade die Logs beim Start der Seite
    loadVehicleLogs(currentPage);
});
});


</script>
</body>
</html>
