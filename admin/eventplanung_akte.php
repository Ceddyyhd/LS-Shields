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
                                            <a class="float-right" data-toggle="tooltip" title="Telefonnummer: <?= htmlspecialchars($event['event_lead_phone']); ?>"><?= htmlspecialchars($event['event_lead_name']); ?></a>
                                        </li>
                                    </ul>

                                    <script>
                                        $(document).ready(function () {
                                            // Initialisiere alle Tooltips auf der Seite
                                            $('[data-toggle="tooltip"]').tooltip();
                                        });
                                    </script>

                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ansprechpartner-bearbeiten">
                                        Bearbeiten
                                    </button>

                                    <!-- Modal für Ansprechpartner bearbeiten -->
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
                                                        <input type="hidden" name="event_id" id="event_id" value="<?= $_GET['id']; ?>">
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
                                                            <input type="text" class="form-control datetimepicker-input" name="datum_uhrzeit_event" id="datum_uhrzeit_event" data-target="#datetimepicker" />
                                                            <div class="input-group-append" data-target="#datetimepicker" data-toggle="datetimepicker">
                                                                <div class="input-group-text"><i class="fa fa-calendar"></i> </div>
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
                                                                foreach ($users as $user) {
                                                                    $selected = ($user['id'] == $event['event_lead']) ? 'selected' : '';
                                                                    echo "<option value='{$user['id']}' {$selected}>{$user['name']}</option>";
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

                                            // Event ID aus der URL extrahieren
                                            var urlParams = new URLSearchParams(window.location.search);
                                            var event_id = urlParams.get('id');

                                            // Formular per AJAX senden
                                            $('#edit-form').on('submit', function (e) {
                                                e.preventDefault();

                                                var formData = $(this).serialize();
                                                formData += '&event_id=' + event_id;

                                                $.ajax({
                                                    url: 'include/update_event.php',
                                                    method: 'POST',
                                                    data: formData,
                                                    success: function (response) {
                                                        var responseData = JSON.parse(response);
                                                        $('#ansprechpartner-bearbeiten').modal('hide');
                                                        window.location.reload();
                                                    },
                                                    error: function () {}
                                                });
                                            });
                                        });
                                    </script>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-9">
                            <div class="card">
                                <div class="card-header p-2">
                                    <ul class="nav nav-pills">
                                        <li class="nav-item"><a class="nav-link active" href="#plan" data-toggle="tab">Plan</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#plan-bearbeiten" data-toggle="tab">Plan Bearbeiten</a></li>
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
                                                    $('#summernote').summernote({
                                                        height: 300,
                                                        focus: true
                                                    });

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
                                        <!-- Weiterer Inhalt ... -->
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
    <!-- Weitere Skripte ... -->
</body>

</html>
