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
    <p class="text-muted text-center">Ausbildung</p>
    <h3 class="profile-username text-center"><?= htmlspecialchars($event['event']); ?></h3>




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
                                        <li class="nav-item"><a class="nav-link" href="#plan-bearbeiten" data-toggle="tab">Plan Bearbeiten</a></li>
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
