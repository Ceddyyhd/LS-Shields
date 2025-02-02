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
        // Event-Daten mit Event Lead Name und Telefonnummer abfragen
        $stmt = $conn->prepare("SELECT eventplanung.*, users.name AS event_lead_name, users.nummer AS event_lead_phone
                                FROM eventplanung 
                                LEFT JOIN users ON eventplanung.event_lead = users.id 
                                WHERE eventplanung.id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Ergebnis abrufen
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$event) {
            die('Eventplanung nicht gefunden.');
        }

        // Benutzer aus der `users`-Tabelle abfragen
        $userStmt = $conn->prepare("SELECT id, name FROM users");
        $userStmt->execute();
        $users = $userStmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        die("Fehler beim Abrufen der Daten: " . $e->getMessage());
    }
    ?>

    <script>
        $(document).ready(function() {
            // Werte in das Formular setzen
            $('#vorname_nachname').val(<?= json_encode($event['vorname_nachname']); ?>);
            $('#telefonnummer').val(<?= json_encode($event['telefonnummer']); ?>);
            $('#datum_uhrzeit_event').val(<?= json_encode($event['datum_uhrzeit_event']); ?>);
            $('#ort').val(<?= json_encode($event['ort']); ?>);

            // Event Lead setzen
            $('#event_lead').val(<?= json_encode($event['event_lead']); ?>); // Die ID des Event Leads wird hier gesetzt
        });
    </script>

    <?php include 'include/header.php'; ?>
</head>

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
    </div>
    <p class="text-muted text-center">Event</p>
    <h3 class="profile-username text-center"><?= htmlspecialchars($event['event']); ?></h3>
    <p class="text-muted text-center">Anmerkung</p>
    <h3 class="profile-username text-center"><?= htmlspecialchars($event['anmerkung']); ?></h3>
    <p class="text-muted text-center">Ansprechpartner</p>
    <h3 class="profile-username text-center"><?= htmlspecialchars($event['vorname_nachname']); ?></h3>

    <ul class="list-group list-group-unbordered mb-3">
        <li class="list-group-item">
            <b>Tel. Nr.:</b> <a class="float-right"><?= htmlspecialchars($event['telefonnummer']); ?></a>
        </li>
        <li class="list-group-item">
            <b>Datum & </br>Uhrzeit:</b> <a class="float-right"><?= htmlspecialchars($event['datum_uhrzeit_event']); ?></a>
        </li>
        <li class="list-group-item">
            <b>Ort:</b> <a class="float-right"><?= htmlspecialchars($event['ort']); ?></a>
        </li>
        <li class="list-group-item">
            <b>Eventlead:</b> 
            <a class="float-right" 
            data-toggle="tooltip" 
            title="Telefonnummer: <?= htmlspecialchars($event['event_lead_phone']); ?>"><?= htmlspecialchars($event['event_lead_name']); ?></a>
        </li>
        <script>
            $(document).ready(function () {
// Initialisiere alle Tooltips auf der Seite
$('[data-toggle="tooltip"]').tooltip();
});
        </script>
    </ul>
    <?php if ($_SESSION['permissions']['event_planung_bearbeiten'] ?? false): ?>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ansprechpartner-bearbeiten">
      Bearbeiten
    </button>
    <?php endif; ?>


    <div class="modal fade" id="ansprechpartner-bearbeiten">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<h4 class="modal-title">Ansprechpartner bearbeiten</h4>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>
</div>
<div class="modal-body">

<form id="edit-form">
<input type="hidden" name="event_id" id="event_id" value="<?= $_GET['id']; ?>"> <!-- Verwendung von $_GET['id'] -->
<div class="form-group">
<label>Ansprechpartner Name</label>
<input type="text" class="form-control" name="vorname_nachname" id="vorname_nachname">
</div>

<div class="form-group">
<label>Ansprechpartner Tel. Nr.:</label>
<input type="text" class="form-control" name="telefonnummer" id="telefonnummer">
</div>

<label>Datum & Uhrzeit:</label>
<div class="input-group date" id="datetimepicker" data-target-input="nearest">
<input type="text" class="form-control datetimepicker-input" name="datum_uhrzeit_event" id="datum_uhrzeit_event" data-target="#datetimepicker"/>
<div class="input-group-append" data-target="#datetimepicker" data-toggle="datetimepicker">
<div class="input-group-text"><i class="fa fa-calendar"></i> </div> <!-- Uhr-Icon hier hinzufügen -->
</div>
</div>

<div class="form-group">
<label>Ort</label>
<input type="text" class="form-control" name="ort" id="ort">
</div>

<div class="form-group">
<label>Event Lead</label>
<select class="form-control" name="event_lead" id="event_lead">
<?php
// Alle Benutzer im Dropdown anzeigen
foreach ($users as $user) {
$selected = ($user['id'] == $event['event_lead']) ? 'selected' : '';
echo "<option value='{$user['id']}' {$selected}>{$user['name']}</option>";
}
?>
</select>
</div>

<div class="form-group">
    <label>Status</label>
    <select class="form-control" name="status" id="status">
        <?php
        // Angenommene Variable $event['status'] enthält den aktuellen Status des Events
        $statusOptions = ['in Planung', 'Abgeschlossen', 'Gelöscht'];

        // Durch die Status-Optionen iterieren und den Status als ausgewählt markieren
        foreach ($statusOptions as $status) {
            // Wenn der Status "Gelöscht" ist, wird er nicht auswählbar
            $disabled = ($status == 'Gelöscht') ? 'disabled' : '';  
            $selected = ($status == $event['status']) ? 'selected' : '';  // Vergleiche mit dem aktuellen Event-Status
            echo "<option value='{$status}' {$selected} {$disabled}>{$status}</option>";
        }
        ?>
    </select>
</div>


<div class="form-group">
<label>Event</label>
<input type="text" class="form-control" name="event" id="event" value="<?= htmlspecialchars($event['event']); ?>">
</div>

<div class="form-group">
<label>Anmerkung</label>
<textarea class="form-control" name="anmerkung" id="anmerkung" rows="3"><?= htmlspecialchars($event['anmerkung']); ?></textarea>
</div>

<div class="modal-footer justify-content-between">
<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
<button type="submit_update" class="btn btn-primary">Save changes</button>
</div>
</form>

</div>
</div>
</div>
</div>
<script>
$(document).ready(function () {
$('#datetimepicker').datetimepicker({
format: 'YYYY-MM-DD HH:mm', // Format für MySQL
icons: { // Hier kannst du die Icons für den Kalender und die Uhr setzen
time: 'fa fa-clock',
date: 'fa fa-calendar',
up: 'fa fa-arrow-up',
down: 'fa fa-arrow-down',
previous: 'fa fa-chevron-left',
next: 'fa fa-chevron-right'
}
});

// Event ID aus der URL extrahieren
var urlParams = new URLSearchParams(window.location.search);
var event_id = urlParams.get('id'); // Event ID aus URL holen

// Formular per AJAX senden
$('#edit-form').on('submit', function(e) {
e.preventDefault(); // Verhindert das normale Absenden des Formulars

var formData = $(this).serialize(); // Alle Formulardaten serialisieren
formData += '&event_id=' + event_id; // Event ID zur Formulardaten hinzufügen

$.ajax({
url: 'include/update_event.php', // PHP-Skript zum Verarbeiten des Updates
method: 'POST',
data: formData,
success: function(response) {
    var responseData = JSON.parse(response);
    $('#ansprechpartner-bearbeiten').modal('hide'); // Modal schließen
    window.location.reload();  // Seite neu laden
},
error: function() {
}
});
});
});


</script>


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


<style>
/* Flexbox für das Layout der Teams */
.row {
display: flex;
flex-wrap: wrap; /* Damit bei Bedarf Zeilenumbruch möglich ist */
justify-content: flex-start; /* Ausrichtung nach links */
}

.row dt {
flex: 0 0 30%; /* Den Teamnamen mit einer festen Breite */
white-space: nowrap; /* Verhindert das Umbruchverhalten */
}

.row dd {
flex: 1 0 60%; /* Den Mitarbeitern genügend Platz geben */
}

.row li {
list-style-type: none; /* Entfernt das Standard-Aufzählungszeichen */
}

.row dt, .row dd {
margin: 0; /* Verhindert unnötige Abstände */
}

.row p {
margin: 0;
}
</style>                <script>
$(document).ready(function() {
var eventId = <?php echo $_GET['id']; ?>; // Event ID aus der URL

// AJAX-Anfrage, um die Team-Daten direkt beim Laden der Seite zu laden
$.ajax({
url: 'include/team_get.php', 
method: 'GET',
data: { event_id: eventId },
dataType: 'json',
success: function(response) {
console.log("Serverantwort (raw):", response); // Gibt die rohen Daten aus

if (Array.isArray(response) && response.length > 0) {
    // Leere das <dl>-Tag
    $('#teams-page-container').empty(); // Entfernt alle vorherigen Teams

    // Füge die Team-Daten in das <dl> ein
    response.forEach(function(team, index) {
        const teamName = team.team_name;
        const teamArea = team.area_name;

        // Erstelle die Liste der Mitarbeiter mit speziellen Formatierungen für den Teamlead
        const teamEmployees = team.employee_names.map(employee => {
            if (employee.is_team_lead == 1) {
                // Wenn es der Teamlead ist, wende das Styling an
                return `<li><p><font style="background-color: rgb(148, 189, 123);" color="#000000">${employee.name}</font></p></li>`;
            } else {
                // Für normale Mitarbeiter
                return `<li>${employee.name}</li>`;
            }
        }).join(''); // Liste der Mitarbeiter

        // Dynamisch in das <dl> einfügen
        const teamHtml = `
            <dt class="col-sm-4">${teamName} (${teamArea})</dt>
            <dd class="col-sm-8"> 
                <ul>
                    ${teamEmployees}
                </ul>
            </dd>
        `;
        $('#teams-page-container').append(teamHtml);
    });
} else {
    console.log("Keine Teams gefunden.");
}
},
error: function(xhr, status, error) {
console.log('Fehler bei der Anfrage:', error);
console.log('Antwort des Servers: ', xhr.responseText); // Gibt die vollständige Antwort des Servers aus
}
});
});
</script>

<div class="card-body">
<dl class="row" id="teams-page-container">
<!-- Dynamisch generierte Inhalte erscheinen hier -->
</dl>
<?php if ($_SESSION['permissions']['event_teams_bearbeiten'] ?? false): ?>
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#teams-bearbeiten">
Teams Bearbeiten
    </button>
    <?php endif; ?>

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
$(document).ready(function() {
$('#saveTeam').click(function() {
const teamData = [];
$('.team-form').each(function(index) {
const teamName = $(this).find('input[name^="team_name"]').val();
const teamArea = $(this).find('input[name^="bereich"]').val();
const employees = [];
$(this).find('input[name^="mitarbeiter_"]').each(function(empIndex) {
    const employeeName = $(this).val().trim(); // Leerzeichen entfernen
    const isTeamLead = empIndex === 0 ? "1" : "0"; // Der erste Mitarbeiter ist Team Lead
    if (employeeName !== '') {
        employees.push({
            name: employeeName,
            is_team_lead: isTeamLead
        });
    }
});

if (employees.length > 0) {
    const team = {
        team_name: teamName,
        area_name: teamArea,
        employee_names: employees
    };
    teamData.push(team);
}
});

// Holen der Event-ID aus der URL
var eventId = <?php echo isset($_GET['id']) ? $_GET['id'] : 'null'; ?>;
if (eventId === null) {
return;
}

console.log("Event ID:", eventId);  // Die ID aus der URL ausgeben
console.log("TeamData vor dem Senden:", teamData);  // Gibt die zu sendenden Daten aus

// AJAX-Anfrage
$.ajax({
url: 'include/team_assignments.php', 
method: 'POST',
data: {
    teams: teamData,
    event_id: eventId  // Event-ID hinzufügen
},
success: function(response) {
    console.log('Erfolgreich gespeichert:', response);
    location.reload();
},
error: function(xhr, status, error) {
    console.log('Fehler bei der Anfrage:', error);
}
});
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

                        <div class="col-md-9">
                            <div class="card">
                                <div class="card-header p-2">
                                    <ul class="nav nav-pills">
                                        <li class="nav-item"><a class="nav-link active" href="#plan" data-toggle="tab">Plan</a></li>
                                        <?php if ($_SESSION['permissions']['event_plan_bearbeiten'] ?? false): ?>
                                        <li class="nav-item"><a class="nav-link" href="#plan-bearbeiten" data-toggle="tab">Plan Bearbeiten</a></li>
                                        <?php endif; ?>
                                        <li class="nav-item"><a class="nav-link" href="#anmeldung" data-toggle="tab">Anmeldung</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#dienstplan" data-toggle="tab">Dienstplan</a></li>
                                    </ul>
                                </div><!-- /.card-header -->

                                <div class="card-body">
                                    <div class="tab-content">
                                        <div class="active tab-pane" id="plan">
                                            <?= $event['summernote_content'] ?>
                                        </div>

                                        <div class="tab-pane" id="plan-bearbeiten">
                                            <form action="speichern_eventplanung_summernote.php" method="POST">
                                                <div class="form-group">
                                                    <label for="summernote">Anfrage:</label>
                                                    <textarea id="summernote" name="summernoteContent"><?= htmlspecialchars($event['summernote_content']) ?></textarea>
                                                </div>

                                                <input type="hidden" name="id" value="<?= $event['id'] ?>">

                                                <button type="button" id="submitForm" class="btn btn-danger">Speichern</button>
                                            </form>

                                            <script>
                                                $(document).ready(function () {

    $('#submitForm').on('click', function () {
        var summernoteContent = $('#summernote').val();

        $.ajax({
            url: 'include/speichern_eventplanung_summernote.php',
            type: 'POST',
            data: {
                summernoteContent: summernoteContent,
                id: <?= $event['id'] ?>
            },
            success: function (response) {
                location.reload();
            },
            error: function (xhr, status, error) {
                console.log('Fehler:', error);
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
                                                                    include('include/db.php');
                                                                    $eventId = $_GET['id'];

                                                                    try {
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

                                                                        foreach ($employees as $employee) {
                                                                            echo '<option value="' . $employee['id'] . '">' . htmlspecialchars($employee['name']) . '</option>';
                                                                        }
                                                                    } catch (PDOException $e) {
                                                                        echo 'Fehler: ' . $e->getMessage();
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="InputNotiz">Notiz</label>
                                                                <input type="text" class="form-control" id="InputNotiz" name="InputNotiz" placeholder="Notiz">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php if ($_SESSION['permissions']['event_anmelden'] ?? false): ?>
                                            <button type="button" id="submitFormAnmeldung" class="btn btn-danger">Anmelden</button>
                                            <?php endif; ?>

                                        </div>

                                        <script>
                                            $(document).ready(function() {
                                                $('#submitFormAnmeldung').on('click', function() {
                                                    var selectedEmployees = $('select[name="employee_list[]"]').val();
                                                    var notiz = $('#InputNotiz').val();

                                                    console.log('Ausgewählte Mitarbeiter:', selectedEmployees);
                                                    console.log('Notiz:', notiz);

                                                    if (selectedEmployees && selectedEmployees.length > 0) {
                                                        if (typeof selectedEmployees === 'string') {
                                                            selectedEmployees = [selectedEmployees];
                                                        }

                                                        $.ajax({
                                                            url: 'include/anmeldung_speichern.php',
                                                            type: 'POST',
                                                            data: {
                                                                event_id: <?= $_GET['id'] ?>,
                                                                employees: selectedEmployees,
                                                                InputNotiz: notiz
                                                            },
                                                            success: function(response) {
                                                                console.log('Antwort vom Server:', response);
                                                                location.reload();
                                                            },
                                                            error: function(xhr, status, error) {
                                                                console.log('AJAX-Fehler: ', error);
                                                            }
                                                        });
                                                    }
                                                });
                                            });
                                        </script>

                                        <div class="tab-pane" id="dienstplan">
                                            <form class="form-horizontal" method="POST" id="dienstplanForm">
                                                <?php
                                                include('include/db.php');
                                                $eventId = $_GET['id'];

                                                try {
                                                    $stmt = $conn->prepare("
                                                        SELECT 
                                                            u.id, 
                                                            u.name, 
                                                            d.max_time, 
                                                            d.gestartet_um, 
                                                            d.gegangen_um, 
                                                            d.arbeitszeit, 
                                                            d.notizen,
                                                            em.notizen  
                                                        FROM users u
                                                        JOIN event_mitarbeiter_anmeldung em ON em.employee_id = u.id
                                                        LEFT JOIN dienstplan d ON d.employee_id = u.id AND d.event_id = :event_id
                                                        WHERE em.event_id = :event_id
                                                    ");
                                                    $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
                                                    $stmt->execute();
                                                    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);


                                                    foreach ($employees as $employee) {
                                                        $isOwnEmployee = $employee['id'] == $_SESSION['user_id'];  // ID des aktuellen Benutzers
                                                    
                                                ?>
                                                <h4><?php echo htmlspecialchars($employee['name']); ?> (<?php echo htmlspecialchars($employee['notizen']); ?>)</h4>
                                                <?php if ($isOwnEmployee || ($_SESSION['permissions']['eventplanung_abmeldung_alle'] ?? false)): ?>
                                                    <button type="button" class="btn btn-warning" id="removeEmployee_<?php echo $employee['id']; ?>" data-event-id="<?php echo $eventId; ?>" data-employee-id="<?php echo $employee['id']; ?>">Abmelden</button>
                                                <?php endif; ?>

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
                                                    <?php if ($_SESSION['permissions']['event_dienstplan_save'] ?? false): ?>
                                                        <button type="button" id="submitFormDienstplanung" class="btn btn-danger">Speichern</button>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <script>
                                            $(document).ready(function() {
    // Abmelden-Button klicken
    $('.btn-warning').on('click', function() {
        var eventId = $(this).data('event-id');
        var employeeId = $(this).data('employee-id');
        
        // Bestätigung, ob der Benutzer sich wirklich abmelden möchte
        if (confirm('Möchten Sie sich wirklich abmelden?')) {
            // AJAX-Anfrage zum Abmelden
            $.ajax({
                url: 'include/abmelden.php',  // PHP-Skript zum Löschen des Eintrags
                method: 'POST',
                data: {
                    event_id: eventId,
                    employee_id: employeeId
                },
                success: function(response) {
                    // Falls erfolgreich, den Eintrag aus der Anzeige entfernen oder neu laden
                    if (response.success) {
                        alert('Abmeldung erfolgreich.');
                        location.reload();  // Seite neu laden, um die Änderungen anzuzeigen
                    } else {
                        alert('Fehler beim Abmelden: ' + response.error);
                    }
                }
            });
        }
    });
});
                                        </script>
                                        <script>
                                            $(document).ready(function() {
                                                <?php foreach ($employees as $employee) { ?>
                                                    $('#timepicker<?php echo $employee['id']; ?>').datetimepicker({
                                                        format: 'HH:mm',
                                                        useCurrent: false
                                                    });

                                                    $('#gestartetUm<?php echo $employee['id']; ?>').datetimepicker({
                                                        format: 'YYYY-MM-DD HH:mm',
                                                        icons: {
                                                            time: 'fa fa-clock',
                                                            date: 'fa fa-calendar',
                                                            up: 'fa fa-arrow-up',
                                                            down: 'fa fa-arrow-down',
                                                            previous: 'fa fa-chevron-left',
                                                            next: 'fa fa-chevron-right'
                                                        }
                                                    });

                                                    $('#gegangenUm<?php echo $employee['id']; ?>').datetimepicker({
                                                        format: 'YYYY-MM-DD HH:mm',
                                                        icons: {
                                                            time: 'fa fa-clock',
                                                            date: 'fa fa-calendar',
                                                            up: 'fa fa-arrow-up',
                                                            down: 'fa fa-arrow-down',
                                                            previous: 'fa fa-chevron-left',
                                                            next: 'fa fa-chevron-right'
                                                        }
                                                    });
                                                <?php } ?>

                                                $('#submitFormDienstplanung').on('click', function() {
                                                    var formData = $('#dienstplanForm').serialize();

                                                    $.ajax({
                                                        url: 'include/save_dienstplan.php?id=<?php echo $_GET['id']; ?>',
                                                        type: 'POST',
                                                        data: formData,
                                                        dataType: 'json',
                                                        success: function(response) {
                                                            location.reload();
                                                        },
                                                        error: function(xhr, status, error) {
                                                            console.log("Status: " + status);
                                                            console.log("Fehler: " + error);
                                                            console.log("Antwort: " + xhr.responseText);
                                                        }
                                                    });
                                                });
                                            });
                                        </script>
                                    </div>
                                </div><!-- /.card-body -->
                            </div><!-- /.card -->
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </section><!-- /.content -->
        </div><!-- /.content-wrapper -->

        <footer class="main-footer">
            <div class="float-right d-none d-sm-block">
                <b>Version</b> 3.2.0
            </div>
            <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
        </footer>
    </div><!-- ./wrapper -->

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
            $('#summernote').summernote({
    height: 300, // Höhe des Editors
    callbacks: {
        onImageUpload: function(files) {
            // Erstelle FormData-Objekt für den Upload
            var data = new FormData();
            data.append("file", files[0]);

            // Sende das Bild über AJAX an den Server
            $.ajax({
                url: 'include/upload_image.php', // Pfad zu deinem PHP-Upload-Skript
                type: 'POST',
                data: data,
                contentType: false,
                processData: false,
                success: function(response) {
                    var res = JSON.parse(response);
                    if (res.url) {
                        // Füge das Bild in den Editor ein
                        $('#summernote').summernote('insertImage', res.url);
                    } else {
                        alert('Fehler beim Hochladen des Bildes: ' + res.error);
                    }
                }
            });
        }
    }
});
            $('#reservationdate').datetimepicker({
                format: 'L'
            });
            $('#reservationdatetime').datetimepicker({ icons: { time: 'far fa-clock' } });
            $('#reservation').daterangepicker();
            $('#reservationtime').daterangepicker({
                timePicker: true,
                timePickerIncrement: 30,
                locale: {
                    format: 'MM/DD/YYYY hh:mm A'
                }
            });
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
            $('#timepicker').datetimepicker({
                format: 'LT'
            });
            $('select.duallistbox').bootstrapDualListbox({
                moveOnSelect: false
            });
        })
    </script>
</body>

</html>
