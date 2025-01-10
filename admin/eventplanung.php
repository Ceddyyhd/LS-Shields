<!DOCTYPE html>
<html lang="en">
<?php include 'include/header.php'; ?>

<body class="hold-transition sidebar-mini">
<div class="wrapper">

<?php include 'include/navbar.php'; ?>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Events</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Events</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <?php
    // SQL-Abfrage zum Abrufen aller Events mit dem Status 'in Planung'
    $query = "
    SELECT id, event, anmerkung, status, vorname_nachname, datum_uhrzeit
    FROM eventplanung
    WHERE status = 'in Planung'  -- Zeigt nur Events mit dem Status 'in Planung'
    ORDER BY datum_uhrzeit ASC";  // Sortierung nach Datum und Uhrzeit aufsteigend

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

    <!-- Ausgabe der Events -->
    <table class="table table-striped projects">
    <thead>
        <tr>
            <th style="width: 1%">#</th>
            <th style="width: 15%">Ansprechpartner</th>
            <th style="width: 20%">Event</th>
            <th style="width: 20%">Anmerkung</th>
            <th style="width: 15%">Datum & Uhrzeit</th>
            <th style="width: 20%">Team Members</th>
            <th>Status</th>
            <th style="width: 20%">Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php
    // Zähler für die Reihenfolge der ID (wird hier von der kleinsten ID bis zur größten gezählt)
    foreach ($events as $event) {
        // Für jedes Event die Team-Mitglieder abfragen
        $teamQuery = "
            SELECT u.id AS employee_id, u.name, u.profile_image
            FROM event_mitarbeiter_anmeldung eam
            JOIN users u ON eam.employee_id = u.id
            WHERE eam.event_id = :event_id";

        $teamStmt = $conn->prepare($teamQuery);
        $teamStmt->bindParam(':event_id', $event['id'], PDO::PARAM_INT);
        $teamStmt->execute();
        $team_members = $teamStmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<tr>";
        echo "<td>" . htmlspecialchars($event['id']) . "</td>"; // Hier wird die ID der DB angezeigt
        echo "<td><a>" . htmlspecialchars($event['vorname_nachname']) . "</a><br/><small>Created " . date('d.m.Y', strtotime($event['datum_uhrzeit'])) . "</small></td>";
        echo "<td><span>" . htmlspecialchars($event['event']) . "</span></td>";
        echo "<td><span>" . htmlspecialchars($event['anmerkung']) . "</span></td>";
        echo "<td><span>" . htmlspecialchars($event['datum_uhrzeit']) . "</span></td>";

        // Team-Mitglieder anzeigen
        echo "<td><ul class='list-inline'>";
        if (empty($team_members)) {
            echo "<li>No team members available</li>";
        } else {
            foreach ($team_members as $member) {
                echo "<li class='list-inline-item' data-toggle='tooltip' title='" . htmlspecialchars($member['name']) . "'>";
                echo "<img alt='Avatar' class='table-avatar' src='" . htmlspecialchars($member['profile_image']) . "'>";
                echo "</li>";
            }
        }
        echo "</ul></td>";

        // Status anzeigen
        echo "<td class='project-state'>";
        if ($event['status'] == 'in Planung') {
            echo "<span class='badge badge-warning'>In Planung</span>";
        } elseif ($event['status'] == 'in Durchführung') {
            echo "<span class='badge badge-danger'>In Durchführung</span>";
        } elseif ($event['status'] == 'Abgeschlossen') {
            echo "<span class='badge badge-success'>Abgeschlossen</span>";
        }
        echo "</td>";

        // Aktionen
        echo "<td class='project-actions text-right'>";
        echo "<a class='btn btn-primary btn-sm' href='eventplanung_akte.php?id=" . $event['id'] . "'><i class='fas fa-folder'></i> View</a>";

        if (isset($_SESSION['permissions']['eventplanung_delete']) && $_SESSION['permissions']['eventplanung_delete']) {
            // Der Delete-Button wird nur angezeigt, wenn der Benutzer die Berechtigung hat
            echo "<button class='btn btn-info btn-sm duplicate-event' data-id='" . $event['id'] . "'><i class='fas fa-copy'></i> Kopieren</button>";
        }

        if (isset($_SESSION['permissions']['eventplanung_delete']) && $_SESSION['permissions']['eventplanung_delete']) {
            // Der Delete-Button wird nur angezeigt, wenn der Benutzer die Berechtigung hat
            echo "<button class='btn btn-danger btn-sm delete-event' data-id='" . $event['id'] . "'><i class='fas fa-trash'></i> Delete</button>";
        }

        echo "</td>";
        echo "</tr>";
    }
    ?>
    </tbody>
</table>
</div>
    </div>
    <script>
    // Duplizieren-Button Funktion mit jQuery AJAX
    $(document).ready(function() {
        $('.duplicate-event').on('click', function() {
            const eventId = $(this).data('id');
            $.ajax({
                url: ' include/duplicate_event.php',
                type: 'POST',
                data: { event_id: eventId },
                success: function(response) {
                    alert('Event successfully duplicated!');
                    location.reload(); // Seite neu laden, um das duplizierte Event zu sehen
                },
                error: function() {
                    alert('An error occurred while duplicating the event.');
                }
            });
        });
    });
</script>
    <script>
// AJAX Delete Event
$(document).ready(function() {
    // Event-Listener für den Delete-Button
    $('.delete-event').on('click', function() {
        var eventId = $(this).data('id'); // Event-ID aus dem Button-Attribut holen

        // Bestätigungsdialog
        if (confirm("Möchten Sie dieses Event wirklich löschen?")) {
            // AJAX-Anfrage zum Löschen des Events
            $.ajax({
                url: 'include/delete_event.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    event_id: eventId
                },
                success: function(response) {
                    if (response.success) {
                        // Erfolgsmeldung und ggf. die Tabelle aktualisieren
                        alert(response.message);
                        // Optional: Event aus der Anzeige entfernen
                        $('button[data-id="'+eventId+'"]').closest('tr').fadeOut();
                    } else {
                        // Fehlerbehandlung
                        alert(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Es gab einen Fehler beim Löschen des Events.');
                }
            });
        }
    });
});
</script>
    <!-- JavaScript zum Initialisieren der Tooltips -->
    <script>
        $(document).ready(function () {
            // Initialisiere alle Tooltips auf der Seite
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>

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
