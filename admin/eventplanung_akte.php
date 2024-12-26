<!DOCTYPE html>
<html lang="en">

<head>
<!-- summernote -->
<link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- daterange picker -->
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Bootstrap Color Picker -->
  <link rel="stylesheet" href="plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <!-- Bootstrap4 Duallistbox -->
  <link rel="stylesheet" href="plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">
  <!-- BS Stepper -->
  <link rel="stylesheet" href="plugins/bs-stepper/css/bs-stepper.min.css">
  <!-- dropzonejs -->
  <link rel="stylesheet" href="plugins/dropzone/min/dropzone.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote.min.js"></script>

  <?php
// Verbindung zur Datenbank einbinden
include('include/db.php');

// ID aus der URL holen
if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    die('Kein Eventplanungs-ID angegeben.');
}



// SQL-Abfrage zum Abrufen der Eventplanung aus der Datenbank
try {
    $stmt = $conn->prepare("SELECT * FROM eventplanung WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // Ergebnis abrufen
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        die('Eventplanung nicht gefunden.');
    }
} catch (PDOException $e) {
    die("Fehler beim Abrufen der Daten: " . $e->getMessage());
}
?>
<?php include 'include/header.php'; ?>

<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  <?php include 'include/navbar.php'; ?>

  <!-- /.navbar -->

  <!-- Main Sidebar Container -->


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Profile</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">User Profile</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle"
                       src="dist/img/user4-128x128.jpg"
                       alt="User profile picture">
                </div>
                <p class="text-muted text-center">Ansprechpartner</p>
                <h3 class="profile-username text-center">Nina Mcintire</h3>

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>Tel. Nr.:</b> <a class="float-right">555 - 667 7541</a>
                  </li>
                  <li class="list-group-item">
                    <b>Datum & Uhrzeit:</b> <a class="float-right">15.12.2024</a>
                  </li>
                  <li class="list-group-item">
                    <b>Ort:</b> <a class="float-right">15.12.2024</a>
                  </li>
                </ul>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <!-- About Me Box -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Informationen</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <strong><i class="fas fa-book mr-1"></i> Teams</strong>

                <div class="card-body">
                <dl class="row">
                  <dt class="col-sm-4">Haupteingang</dt>
                  <dd class="col-sm-8"> 
                    <ul>
                      <li>Cedric Schmidt</li>
                      <li>John Schmidt</li>
                    </ul>
                </dd>
                  <dt class="col-sm-4">Nebeneingang</dt>
                  <dd class="col-sm-8"> 
                    <ul>
                      <li>Cedric Schmidt</li>
                      <li>John Schmidt</li>
                    </ul>
                </dd>
                  <dt class="col-sm-4">Tür 1</dt>
                  <dd class="col-sm-8"> 
                    <ul>
                      <li>Cedric Schmidt</li>
                      <li>John Schmidt</li>
                    </ul>
                </dd>
                  <dt class="col-sm-4">Tür 2</dt>
                  <dd class="col-sm-8"> 
                    <ul>
                      <li>Cedric Schmidt</li>
                      <li>John Schmidt</li>
                    </ul>
                </dd>

                  </dd>
                </dl>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#teams-bearbeiten">
                  Teams Bearbeiten
                </button>

<!-- Modal für Team-Erstellung -->
<div class="modal fade" id="teams-bearbeiten">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalTitle">Team bearbeiten</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="teams-container">
                    <!-- Standard-Teamformular -->
                    <div class="team-form" id="team-form-1">
                        <div class="form-group">
                            <label for="team_name">Team Name 1</label>
                            <input type="text" class="form-control team_name" name="team_name[]" placeholder="Team Name">
                        </div>
                        <div class="form-group">
                            <label for="bereich">Bereich</label>
                            <input type="text" class="form-control bereich" name="bereich[]" placeholder="Bereich">
                        </div>
                        <div class="form-group" id="mitarbeiter-container">
                            <label for="mitarbeiter">Mitarbeiter</label>
                            <!-- Festes Mitarbeiterfeld für Team Lead -->
                            <div class="input-group mb-3">
                                <input type="text" class="form-control mitarbeiter" name="mitarbeiter_1_0" placeholder="Mitarbeiter (Team Lead)" required>
                            </div>
                            <!-- Dynamisches Mitarbeiterfeld -->
                            <div class="input-group mb-3">
                                <input type="text" class="form-control mitarbeiter" name="mitarbeiter_1_1" placeholder="Mitarbeiter">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Button zum Erstellen eines neuen Teamformulars -->
                <button type="button" class="btn btn-primary" id="createTeam">Neues Team erstellen</button>
            </div>

            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
                <button type="button" class="btn btn-primary" id="saveTeam">Speichern</button>
            </div>
        </div>
    </div>
</div>

<!-- Füge das Skript am Ende der Seite ein -->
<script>
    $(document).ready(function() {
        let teamCount = 1; // Starten mit Team 1
        $('#teams-bearbeiten').on('show.bs.modal', function (e) {
            var eventId = <?php echo $_GET['id']; ?>; // Event ID aus der URL

            // AJAX-Anfrage, um die Team-Daten zu laden
            $.ajax({
                url: 'include/team_get.php', 
                method: 'GET',
                data: { event_id: eventId },
                dataType: 'json',
                success: function(response) {
                    console.log("Serverantwort (raw):", response); // Gibt die rohen Daten aus

                    if (Array.isArray(response) && response.length > 0) {
                        // Leere das Modal
                        $('#teams-container').empty(); // Entfernt alle vorherigen Teams

                        // Füge die Team-Daten in das Modal ein
                        response.forEach(function(team, index) {
                            const teamIndex = index + 1;  // Um die Team-ID korrekt zu benennen (Team Name 1, 2, 3, etc.)
                            $('#teams-container').append(generateTeamForm(team, teamIndex));
                        });
                    } else {
                        console.log("Keine Teams gefunden.");
                        alert('Keine Teams gefunden.');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Fehler bei der Anfrage:', error);
                    console.log('Antwort des Servers: ', xhr.responseText); // Gibt die vollständige Antwort des Servers aus
                }
            });
        });

        // Funktion zum Generieren des HTML für Teamformular
        function generateTeamForm(team, index) {
            let employeeFields = ''; // Variable für die Mitarbeiterfelder

            // Durch alle Mitarbeiter des Teams iterieren
            team.employee_names.forEach(function(employee, empIndex) {
                employeeFields += `
                    <div class="input-group mb-3">
                        <input type="text" class="form-control mitarbeiter" name="mitarbeiter_${index}_${empIndex}[][name]" placeholder="Mitarbeiter" value="${employee.name}" ${empIndex === 0 ? 'required' : ''}>
                        <!-- Versteckte Eingabefelder für Mitarbeiter ID -->
                        <input type="hidden" name="employee_ids[]" value="${employee.id}">
                    </div>
                `;
            });

            // Füge ein leeres Mitarbeiterfeld hinzu, um einen neuen Mitarbeiter hinzuzufügen
            employeeFields += `
                <div class="input-group mb-3">
                    <input type="text" class="form-control mitarbeiter" name="mitarbeiter_${index}_new" placeholder="Mitarbeiter hinzufügen">
                </div>
            `;

            return `
                <div class="team-form" id="team-form-${index}">
                    <hr>
                    <!-- Verstecktes Feld für Team ID -->
                    <input type="hidden" name="team_id[]" value="${team.team_id}">
                    <div class="form-group">
                        <label for="team_name">Team Name ${index}</label>
                        <input type="text" class="form-control team_name" name="team_name[]" placeholder="Team Name" value="${team.team_name}">
                    </div>
                    <div class="form-group">
                        <label for="bereich">Bereich</label>
                        <input type="text" class="form-control bereich" name="bereich[]" placeholder="Bereich" value="${team.area_name}">
                    </div>
                    <div class="form-group" id="mitarbeiter-container">
                        <label for="mitarbeiter">Mitarbeiter</label>
                        ${employeeFields}
                    </div>
                </div>
            `;
        }

        // Dynamisches Hinzufügen von Mitarbeiterfeldern
        $(document).on('input', '.mitarbeiter', function() {
            const parentTeamForm = $(this).closest('.team-form'); // Finde das Teamformular, in dem das Input-Feld ist
            const lastEmployeeField = parentTeamForm.find('.input-group.mb-3').last(); // Das letzte Mitarbeiterfeld im aktuellen Team

            // Wenn das letzte Mitarbeiterfeld ausgefüllt wird, füge ein neues hinzu
            if (lastEmployeeField.find('input').val() !== '') {
                const teamId = parentTeamForm.attr('id'); // Das Team-Id (z.B. team-form-1)
                const newEmployeeField = `
                    <div class="input-group mb-3">
                        <input type="text" class="form-control mitarbeiter" name="mitarbeiter_${teamId}[][name]" placeholder="Mitarbeiter">
                    </div>
                `;
                parentTeamForm.find('#mitarbeiter-container').append(newEmployeeField); // Neues Mitarbeiterfeld im aktuellen Team hinzufügen
            }
        });

        // Neues Team erstellen und das leere Formular unterhalb des aktuellen Formulars hinzufügen
        $('#createTeam').click(function() {
            teamCount++;

            const newTeamForm = `
                <div class="team-form" id="team-form-${teamCount}">
                    <hr>
                    <div class="form-group">
                        <label for="team_name">Team Name ${teamCount}</label>
                        <input type="text" class="form-control team_name" name="team_name[]" placeholder="Team Name">
                    </div>
                    <div class="form-group">
                        <label for="bereich">Bereich</label>
                        <input type="text" class="form-control bereich" name="bereich[]" placeholder="Bereich">
                    </div>
                    <div class="form-group" id="mitarbeiter-container">
                        <label for="mitarbeiter">Mitarbeiter</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control mitarbeiter" name="mitarbeiter_${teamCount}[][name]" placeholder="Mitarbeiter (Team Lead)" required>
                        </div>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control mitarbeiter" name="mitarbeiter_${teamCount}[][name]" placeholder="Mitarbeiter">
                        </div>
                    </div>
                </div>
            `;

            $('#teams-container').append(newTeamForm);
        });

        // Speichern der Teamdaten
        $('#saveTeam').click(function() {
    const teamData = [];

    // Erfassung der Team- und Mitarbeiterdaten
    $('.team-form').each(function(index) {
        const teamName = $(this).find('input[name^="team_name"]').val();
        const teamArea = $(this).find('input[name^="bereich"]').val();

        const employees = [];
        $(this).find('input[name^="mitarbeiter_"]').each(function(empIndex) {
            const employeeName = $(this).val().trim(); // Leerzeichen entfernen
            const isTeamLead = empIndex === 0 ? "1" : "0"; // Der erste Mitarbeiter ist Team Lead

            // Ignoriere leere Mitarbeiter
            if (employeeName !== '') {
                employees.push({
                    name: employeeName,
                    is_team_lead: isTeamLead
                });
            }
        });

        // Füge nur Teams hinzu, die Mitarbeiter haben
        if (employees.length > 0) {
            const team = {
                team_name: teamName,
                area_name: teamArea,
                employee_names: employees
            };

            teamData.push(team);
        }
    });

    console.log("TeamData vor dem Senden:", teamData);

    // Sende die Daten per AJAX an den Server
    $.ajax({
        url: 'include/team_assignments.php', 
        method: 'POST',
        data: { teams: teamData },
        success: function(response) {
            alert('Teams wurden gespeichert.');
        },
        error: function(xhr, status, error) {
            alert('Fehler beim Speichern der Teams.');
        }
    });
});

    });
</script>

              


              </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#plan" data-toggle="tab">Plan</a></li>
                  <li class="nav-item"><a class="nav-link" href="#plan-bearbeiten" data-toggle="tab">Plan Bearbeiten</a></li>
                  <li class="nav-item"><a class="nav-link" href="#anmeldung" data-toggle="tab">Anmeldung</a></li>
                  <li class="nav-item"><a class="nav-link" href="#dienstplan" data-toggle="tab">Dienstplan</a></li>
                  <li class="nav-item"><a class="nav-link" href="#externes-dokument1" data-toggle="tab">Externes Dokument (Benennbar)</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                <div class="active tab-pane" id="plan">
                    <!-- Direkte Ausgabe des gespeicherten HTML-Inhalts -->
                    <?= $event['summernote_content'] ?>
                </div>

                  <div class="active tab-pane" id="plan-bearbeiten">
                  <form action="speichern_eventplanung_summernote.php" method="POST">
        <div class="form-group">
            <label for="summernote">Anfrage:</label>
            <textarea id="summernote" name="summernoteContent"><?= htmlspecialchars($event['summernote_content']) ?></textarea>
        </div>
        
        <input type="hidden" name="id" value="<?= $event['id'] ?>">
        
        <button type="button" id="submitForm" class="btn btn-danger">Speichern</button> <!-- Submit-Button -->
    </form>

    <script>
        $(document).ready(function() {
            // Summernote initialisieren
            $('#summernote').summernote({
                height: 300,   // Höhe von Summernote anpassen
                focus: true     // Fokus auf das Summernote-Feld setzen
            });

            // Submit-Button-Funktionalität
            $('#submitForm').on('click', function() {
                var summernoteContent = $('#summernote').val();  // Den Inhalt von Summernote holen
                console.log(summernoteContent);  // Ausgabe des Inhalts in der Konsole

                // AJAX-Anfrage zum Speichern der Daten
                $.ajax({
                    url: 'include/speichern_eventplanung_summernote.php',  // Das PHP-Skript zum Speichern
                    type: 'POST',
                    data: {
                        summernoteContent: summernoteContent,
                        id: <?= $event['id'] ?>  // ID des Events übergeben
                    },
                    success: function(response) {
                        console.log(response);  // Antwort des Servers in der Konsole
                        alert('Daten wurden gespeichert!');
                    },
                    error: function(xhr, status, error) {
                        console.log('Fehler:', error);  // Fehlerdetails in der Konsole
                        alert('Fehler beim Speichern der Daten!');
                    }
                });
            });
        });
    </script>






                  </div>
                  

                  <div class="tab-pane" id="anmeldung">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label>Eintragen wer kann</label>
                    <div class="form-group">
                        <select class="form-control" name="employee_list[]">
                            <?php
                            // Verbindung zur Datenbank
                            include('include/db.php');

                            // Event ID aus der URL holen
                            $eventId = $_GET['id'];

                            try {
                                // Alle Mitarbeiter holen, die sich noch nicht für das Event angemeldet haben
                                $stmt = $conn->prepare("
                                    SELECT u.id, u.name
                                    FROM users u
                                    WHERE u.gekuendigt = 'no_kuendigung'
                                    AND NOT EXISTS (
                                        SELECT 1 FROM event_mitarbeiter_anmeldung em
                                        WHERE em.employee_id = u.id AND em.event_id = :event_id
                                    )
                                ");
                                $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
                                $stmt->execute();
                                $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                // Mitarbeiter in die Dropdown-Liste einfügen
                                foreach ($employees as $employee) {
                                    echo '<option value="' . $employee['id'] . '">' . htmlspecialchars($employee['name']) . '</option>';
                                }
                            } catch (PDOException $e) {
                                echo 'Fehler: ' . $e->getMessage();
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button type="button" id="submitFormAnmeldung" class="btn btn-danger">Anmelden</button>
</div>


<script>
  $(document).ready(function() {
    // Anmeldung abschicken
    $('#submitFormAnmeldung').on('click', function() {
        var selectedEmployees = $('select[name="employee_list[]"]').val(); // Ausgewählte Mitarbeiter
        console.log('Ausgewählte Mitarbeiter:', selectedEmployees);  // Ausgabe der ausgewählten Mitarbeiter

        // Überprüfen, ob Mitarbeiter ausgewählt wurden
        if (selectedEmployees && selectedEmployees.length > 0) {
            console.log('Mitarbeiter werden gesendet');

            // Sicherstellen, dass selectedEmployees ein Array ist
            if (typeof selectedEmployees === 'string') {
                selectedEmployees = [selectedEmployees]; // Falls es nur eine Auswahl ist, in ein Array umwandeln
            }

            $.ajax({
                url: 'include/anmeldung_speichern.php', // PHP-Skript zum Speichern
                type: 'POST',
                data: {
                    event_id: <?= $_GET['id'] ?>,  // Event ID aus der URL
                    employees: selectedEmployees
                },
                success: function(response) {
                    console.log('Antwort vom Server:', response); // Serverantwort in der Konsole anzeigen
                    alert('Anmeldung erfolgreich!');

                    // Nach erfolgreicher Anmeldung den Mitarbeiter aus der Liste entfernen
                    selectedEmployees.forEach(function(employeeId) {
                        $('option[value="' + employeeId + '"]').remove();
                    });
                },
                error: function(xhr, status, error) {
                    console.log('AJAX-Fehler: ', error);  // Fehlerdetails in der Konsole
                    console.log('Status: ', status);
                    console.log('XHR-Objekt: ', xhr);
                    alert('Fehler bei der Anmeldung!');
                }
            });
        } else {
            alert('Bitte wählen Sie mindestens einen Mitarbeiter aus!');
        }
    });
});
</script>


<div class="tab-pane" id="dienstplan">
    <form class="form-horizontal" method="POST" id="dienstplanForm">
        <?php
        // Verbindung zur Datenbank
        include('db.php');

        // Event ID aus der URL holen
        $eventId = $_GET['id'];

        try {
            // Alle Mitarbeiter holen, die sich für das Event angemeldet haben und bereits Daten im Dienstplan haben
            $stmt = $conn->prepare("
              SELECT u.id, u.name, d.max_time, d.gestartet_um, d.gegangen_um, d.arbeitszeit
              FROM users u
              JOIN event_mitarbeiter_anmeldung em ON em.employee_id = u.id
              LEFT JOIN dienstplan d ON d.employee_id = u.id AND d.event_id = :event_id
              WHERE em.event_id = :event_id
            ");
            $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
            $stmt->execute();
            $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Über alle Mitarbeiter iterieren und für jeden Mitarbeiter ein Formular erstellen
            foreach ($employees as $employee) {
                ?>
                <h4><?php echo htmlspecialchars($employee['name']); ?></h4>
                <div class="form-group">
                    <div class="bootstrap-timepicker">
                        <label>Maximal da bis:</label>
                        <div class="input-group date" id="timepicker<?php echo $employee['id']; ?>" data-target-input="nearest">
                            <input type="text" class="form-control datetimepicker-input" data-target="#timepicker<?php echo $employee['id']; ?>" name="max_time_<?php echo $employee['id']; ?>"
                            value="<?php echo htmlspecialchars($employee['max_time']); ?>"/>
                            <div class="input-group-append" data-target="#timepicker<?php echo $employee['id']; ?>" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="far fa-clock"></i></div>
                            </div>
                        </div>
                    </div>

                    <label>Gestartet Um:</label>
                    <div class="input-group date" id="gestartetUm<?php echo $employee['id']; ?>" data-target-input="nearest">
                        <input type="text" class="form-control datetimepicker-input" data-target="#gestartetUm<?php echo $employee['id']; ?>" name="gestartet_um_<?php echo $employee['id']; ?>"
                        value="<?php echo isset($employee['gestartet_um']) ? $employee['gestartet_um'] : ''; ?>"/>
                        <div class="input-group-append" data-target="#gestartetUm<?php echo $employee['id']; ?>" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>

                    <label>Gegangen Um:</label>
                    <div class="input-group date" id="gegangenUm<?php echo $employee['id']; ?>" data-target-input="nearest">
                        <input type="text" class="form-control datetimepicker-input" data-target="#gegangenUm<?php echo $employee['id']; ?>" name="gegangen_um_<?php echo $employee['id']; ?>"
                        value="<?php echo isset($employee['gegangen_um']) ? $employee['gegangen_um'] : ''; ?>"/>
                        <div class="input-group-append" data-target="#gegangenUm<?php echo $employee['id']; ?>" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                    <!-- Hier die Arbeitszeit anzeigen -->
                    <label>Std. Gearbeitet:</label>
                    <p>
                        <?php echo ($employee['arbeitszeit'] !== null) ? number_format($employee['arbeitszeit'], 2) . ' Stunden' : 'Noch nicht berechnet'; ?>
                    </p>
                </div>
                <?php
            }
        } catch (PDOException $e) {
            echo 'Fehler: ' . $e->getMessage();
        }
        ?>

        <div class="form-group row">
            <div class="offset-sm-2 col-sm-10">
                <button type="button" id="submitFormDienstplanung" class="btn btn-danger">Speichern</button>
            </div>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    // Initialisiere datetimepicker für jedes Eingabefeld
    <?php foreach ($employees as $employee) { ?>
        $('#timepicker<?php echo $employee['id']; ?>').datetimepicker({
          format: 'HH:mm', // MySQL-kompatibles Format
          useCurrent: false
                });

        $('#gestartetUm<?php echo $employee['id']; ?>').datetimepicker({
            format: 'YYYY-MM-DD HH:mm', // MySQL-kompatibles Format
            useCurrent: false
        });

        $('#gegangenUm<?php echo $employee['id']; ?>').datetimepicker({
            format: 'YYYY-MM-DD HH:mm', // MySQL-kompatibles Format
            useCurrent: false
        });
    <?php } ?>

    // Submit-Button für den Dienstplan
    $('#submitFormDienstplanung').on('click', function() {
        var formData = $('#dienstplanForm').serialize();  // Alle Formulardaten sammeln

        $.ajax({
            url: 'include/save_dienstplan.php?id=<?php echo $_GET['id']; ?>',  // PHP-Skript zum Speichern
            type: 'POST',
            data: formData,  // Alle Formulardaten senden
            dataType: 'json',
            success: function(response) {
                if (response.status == 'success') {
                    alert(response.message);  // Erfolgsmeldung anzeigen
                } else {
                    alert('Fehler: ' + response.message);  // Fehlermeldung anzeigen
                }
            },
            error: function(xhr, status, error) {
              console.log("Status: " + status);  // Gibt den Status des Fehlers aus
              console.log("Fehler: " + error);   // Gibt den Fehlertext aus
              console.log("Antwort: " + xhr.responseText);  // Gibt die vollständige Antwort des Servers aus
              alert('Fehler bei der Anfrage! Siehe Konsole für Details.');
          }
        });
    });
});

</script>

                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 3.2.0
    </div>
    <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Summernote -->
<script src="plugins/summernote/summernote-bs4.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Select2 -->
<script src="plugins/select2/js/select2.full.min.js"></script>
<!-- Bootstrap4 Duallistbox -->
<script src="plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
<!-- InputMask -->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/inputmask/jquery.inputmask.min.js"></script>
<!-- date-range-picker -->
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<!-- bootstrap color picker -->
<script src="plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Bootstrap Switch -->
<script src="plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<!-- BS-Stepper -->
<script src="plugins/bs-stepper/js/bs-stepper.min.js"></script>
<!-- dropzonejs -->
<script src="plugins/dropzone/min/dropzone.min.js"></script>
<script>
  $(function () {
    // Summernote
    $('#summernote').summernote({
        height:500,
    })
    //Date picker
    $('#reservationdate').datetimepicker({
        format: 'L'
    });

    //Date and time picker
    $('#reservationdatetime').datetimepicker({ icons: { time: 'far fa-clock' } });

    //Date range picker
    $('#reservation').daterangepicker()
    //Date range picker with time picker
    $('#reservationtime').daterangepicker({
      timePicker: true,
      timePickerIncrement: 30,
      locale: {
        format: 'MM/DD/YYYY hh:mm A'
      }
    })
    //Date range as a button
    $('#daterange-btn').daterangepicker(
      {
        ranges   : {
          'Today'       : [moment(), moment()],
          'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month'  : [moment().startOf('month'), moment().endOf('month')],
          'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(29, 'days'),
        endDate  : moment()
      },
      function (start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
      }
    )

    //Timepicker
    $('#timepicker').datetimepicker({
      format: 'LT'
    })
    //Bootstrap Duallistbox
    $('select.duallistbox').bootstrapDualListbox({
        moveOnSelect: false
    });
  })
</script>
</body>
</html>