<?php
session_start();  // Stelle sicher, dass die Session gestartet ist
include 'include/db.php';

// Überprüfe, ob der Benutzer angemeldet ist und die Berechtigung besitzt
if (!isset($_SESSION['user_id'])) {
    die("Zugriff verweigert. Du bist nicht angemeldet.");
}

// Hole die Benutzer-ID aus der URL (von der Seite, die aufgerufen wird)
$user_id = $_GET['id'] ?? null;

// Überprüfen, ob die Benutzer-ID in der URL gesetzt ist
if (!$user_id) {
    die("Benutzer-ID fehlt.");
}

// Wenn der Benutzer nicht seine eigene Seite aufruft und keine Berechtigung hat, leite ihn weiter
if ($user_id != $_SESSION['user_id'] && !isset($_SESSION['permissions']['access_all_mitarbeiter_akten'])) {
    die("Zugriff verweigert. Du hast keine Berechtigung, diese Seite zu sehen.");
}

// Benutzerinformationen abrufen
$sql = "SELECT users.*, roles.name AS role_name 
        FROM users 
        LEFT JOIN roles ON users.role_id = roles.id 
        WHERE users.id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Benutzer nicht gefunden.");
}

// Dokumente abrufen
$sql_documents = "SELECT id, file_name, file_path, uploaded_at FROM documents WHERE user_id = :user_id";
$stmt_documents = $conn->prepare($sql_documents);
$stmt_documents->execute(['user_id' => $user_id]);
$documents = $stmt_documents->fetchAll(PDO::FETCH_ASSOC);

// Ausrüstung abrufen
$sql_equipment = "SELECT equipment_name, received FROM equipment WHERE user_id = :user_id";
$stmt_equipment = $conn->prepare($sql_equipment);
$stmt_equipment->execute(['user_id' => $user_id]);
$equipment = $stmt_equipment->fetchAll(PDO::FETCH_ASSOC);

// Notizen abrufen
$sql_notes = "SELECT type, content, created_at, author FROM notes WHERE user_id = :user_id ORDER BY created_at DESC";
$stmt_notes = $conn->prepare($sql_notes);
$stmt_notes->execute(['user_id' => $user_id]);
$notes = $stmt_notes->fetchAll(PDO::FETCH_ASSOC);



// Rechte des Benutzers abrufen
$sql_permissions = "SELECT p.name, p.description, p.display_name 
                    FROM permissions p
                    JOIN roles r ON r.id = :role_id";
$stmt_permissions = $conn->prepare($sql_permissions);
$stmt_permissions->execute(['role_id' => $user['role_id']]);
$permissions = $stmt_permissions->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<?php include 'include/header.php'; ?>

<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  <?php include 'include/navbar.php'; ?>

  <!-- Main Sidebar Container -->
<!-- jQuery (notwendig für Bootstrap) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

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
                  <img class="profile-user-img img-fluid img-circle" src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="User profile picture">
                </div>

                <h3 class="profile-username text-center">
                  <?php echo htmlspecialchars($user['name']); ?>
                </h3>








                <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

                <p class="text-muted text-center">
                <?php
// Hole den 'role_value' des aktuell eingeloggten Benutzers aus der Session
$currentUserRoleValue = $_SESSION['user_role_value']; // Der aktuelle Benutzerwert aus der Session

// Hole den 'role_value' des Benutzers, den du bearbeiten möchtest
$targetUserRoleValue = $user['role_value']; // Angenommen, der Benutzer, den du bearbeiten möchtest, hat das Feld 'role_value'
?>

<?php echo htmlspecialchars($user['role_name']); ?>

<?php if ($_SESSION['permissions']['edit_employee_rank'] ?? false && $targetUserRoleValue < $currentUserRoleValue): ?>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#rang-bearbeiten" style="width: 50px; height: 30px; margin-left: 10px;">
        <i class="fa-solid fa-pen"></i>
    </button>
<?php endif; ?>

<div class="modal fade" id="rang-bearbeiten">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Rang ändern</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="changeRankForm">
                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id']); ?>">
                    <div class="form-group">
                        <label for="roleSelect">Neuer Rang</label>
                        <select class="custom-select" name="role_id" id="roleSelect" required>
                        <?php
                            // Hole den aktuellen Benutzer-Rollenwert aus der Session
                            $currentUserRoleValue = $_SESSION['user_role_value']; // Beispiel: aus der Session holen

                            // Hole alle Rollen, deren 'value' kleiner ist als der des aktuellen Benutzers
                            $query = "SELECT id, name FROM roles WHERE value < :currentRoleValue ORDER BY value DESC";
                            $stmt = $conn->prepare($query);
                            $stmt->bindParam(':currentRoleValue', $currentUserRoleValue, PDO::PARAM_INT);
                            $stmt->execute();
                            $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            // Gebe die Rollen aus
                            foreach ($roles as $role) {
                                echo '<option value="' . $role['id'] . '">' . htmlspecialchars($role['name']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
                <button type="button" class="btn btn-primary" id="saveRankButton">Speichern</button>
            </div>
        </div>
    </div>
</div>

<script>$(document).ready(function () {
    $("#saveRankButton").on("click", function () {
        var formData = $("#changeRankForm").serialize();

        $.ajax({
            url: "include/change_rank.php",
            type: "POST",
            data: formData,
            success: function (response) {
                try {
                    response = JSON.parse(response);
                    if (response.success) {
                        alert(response.message);
                        $("#rang-bearbeiten").modal("hide");
                        location.reload(); // Seite neu laden, um Änderungen anzuzeigen
                    } else {
                        alert("Fehler: " + response.message);
                    }
                } catch (error) {
                    console.error("Fehler beim Parsen der Antwort:", error);
                }
            },
            error: function (xhr, status, error) {
                console.error("Fehler:", error);
                alert("Es ist ein Fehler aufgetreten.");
            },
        });
    });
});</script>








                </a>
                </p>

                <ul class="list-group list-group-unbordered mb-3">
                <li class="list-group-item">
                    <b>Tel. Nr.:</b> <a class="float-right"><?php echo htmlspecialchars($user['nummer']); ?></a>
                </li>
                <li class="list-group-item">
                    <b>Erstellt am:</b> <a class="float-right">
                        <?php echo htmlspecialchars($user['created_at']); ?>
                    </a>
                </li>
            </ul>
            <?php
// Überprüfen, ob der Benutzer als anwesend oder abwesend in der Tabelle gespeichert ist
$user_id = $user['id']; // Benutzer-ID
$attendanceStatus = 'none'; // Standardstatus (kein Eintrag)

// SQL-Abfrage, um den Anwesenheitsstatus des Benutzers zu überprüfen
$stmt = $conn->prepare("SELECT status FROM attendance WHERE user_id = :user_id ORDER BY timestamp DESC LIMIT 1");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$attendanceStatusRow = $stmt->fetch(PDO::FETCH_ASSOC);

// Wenn ein Anwesenheitsstatus vorhanden ist, setze den Status
if ($attendanceStatusRow) {
    $attendanceStatus = $attendanceStatusRow['status'];
}
?>
            <!-- Anwesend / Abwesend Buttons -->
            <div class="form-group">
    <?php if ($attendanceStatus === 'absent' || $attendanceStatus === 'none'): ?>
        <!-- Wenn der Benutzer abwesend ist oder kein Eintrag vorhanden ist, zeigt den "Anwesend"-Button -->
        <button class="btn btn-success" id="presentButton" data-user-id="<?= $user['id']; ?>">Anwesend</button>
    <?php endif; ?>

    <?php if ($attendanceStatus === 'present'): ?>
        <!-- Wenn der Benutzer anwesend ist, zeigt nur den "Abwesend"-Button -->
        <button class="btn btn-danger" id="absentButton" data-user-id="<?= $user['id']; ?>">Abwesend</button>
    <?php endif; ?>
</div>
            <script>
                $(document).ready(function() {
    $('#presentButton').click(function() {
        var userId = $(this).data('user-id');
        $.ajax({
            url: 'include/attendance.php',
            type: 'POST',
            data: { user_id: userId, status: 'present' },
            success: function(response) {
                location.reload();  // Lädt die Seite neu
            }
        });
    });

    $('#absentButton').click(function() {
        var userId = $(this).data('user-id');
        $.ajax({
            url: 'include/attendance.php',
            type: 'POST',
            data: { user_id: userId, status: 'absent' },
            success: function(response) {
                location.reload();  // Lädt die Seite neu
            }
        });
    });
});

            </script>
              </div>
            </div>
          <!-- About Me Box -->
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Information</h3>
          </div>
          <div class="card-body">
            <strong><i class="fas fa-envelope mr-1"></i> Email</strong>
            <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>

            <hr>
            <strong><i class="fas fa-envelope mr-1"></i> UMail</strong>
            <p class="text-muted"><?php echo htmlspecialchars($user['umail']); ?></p>

            <hr>
            <strong><i class="fas fa-credit-card mr-1"></i> Kontonummer</strong>
            <p class="text-muted"><?php echo htmlspecialchars($user['kontonummer']); ?></p>

            <hr>
            <strong><i class="far fa-file-alt mr-1"></i> Letzte Beförderung durch</strong>
            <p class="text-muted"><?php echo htmlspecialchars($user['rank_last_changed_by']); ?>
            </p>
          </div>
          
          <?php if ($_SESSION['permissions']['edit_employee'] ?? false): ?>

          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#user-bearbeiten">
                  User Bearbeiten
                </button>
          <button type="button" id="saveButton" class="btn btn-block btn-primary">Speichern</button>
          <?php endif; ?>
        </div>
      </div>
      <form id="userEditForm">
    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id']); ?>">
    <div class="modal fade" id="user-bearbeiten">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Benutzer bearbeiten</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Information</h3>
                        </div>
                        <div class="card-body">

                            <!-- Name -->
                            <div class="form-group">
                                <strong><i class="fas fa-user mr-1"></i> Name</strong>
                                <?php if ($_SESSION['permissions']['edit_name'] ?? false): ?>
                                    <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($user['name']); ?>">
                                <?php else: ?>
                                    <input type="text" class="form-control" placeholder="<?php echo htmlspecialchars($user['name']); ?>" disabled>
                                <?php endif; ?>
                            </div>

                            <!-- Nummer -->
                            <div class="form-group">
                                <strong><i class="fas fa-phone mr-1"></i> Nummer</strong>
                                <?php if ($_SESSION['permissions']['edit_nummer'] ?? false): ?>
                                    <input type="text" class="form-control" name="nummer" value="<?php echo htmlspecialchars($user['nummer']); ?>">
                                <?php else: ?>
                                    <input type="text" class="form-control" placeholder="<?php echo htmlspecialchars($user['nummer']); ?>" disabled>
                                <?php endif; ?>
                            </div>

                            <!-- Email -->
                            <div class="form-group">
                                <strong><i class="fas fa-envelope mr-1"></i> Email</strong>
                                <?php if ($_SESSION['permissions']['edit_email'] ?? false): ?>
                                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
                                <?php else: ?>
                                    <input type="email" class="form-control" placeholder="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                                <?php endif; ?>
                            </div>

                            <!-- UMail -->
                            <div class="form-group">
                                <strong><i class="fas fa-envelope mr-1"></i> UMail</strong>
                                <?php if ($_SESSION['permissions']['edit_umail'] ?? false): ?>
                                    <input type="text" class="form-control" name="umail" value="<?php echo htmlspecialchars($user['umail']); ?>">
                                <?php else: ?>
                                    <input type="text" class="form-control" placeholder="<?php echo htmlspecialchars($user['umail']); ?>" disabled>
                                <?php endif; ?>
                            </div>

                            <!-- Kontonummer -->
                            <div class="form-group">
                                <strong><i class="fas fa-credit-card mr-1"></i> Kontonummer</strong>
                                <?php if ($_SESSION['permissions']['edit_kontonummer'] ?? false): ?>
                                    <input type="text" class="form-control" name="kontonummer" value="<?php echo htmlspecialchars($user['kontonummer']); ?>">
                                <?php else: ?>
                                    <input type="text" class="form-control" placeholder="<?php echo htmlspecialchars($user['kontonummer']); ?>" disabled>
                                <?php endif; ?>
                            </div>
                            <!-- Passwort ändern -->
                            <div class="form-group">
                                <strong><i class="fas fa-lock mr-1"></i> Passwort ändern</strong>
                                <div class="form-check">
                                    <input type="checkbox" id="changePasswordCheckbox" class="form-check-input">
                                    <label for="changePasswordCheckbox" class="form-check-label">Passwort ändern</label>
                                </div>
                                <input type="password" id="passwordField" name="password" class="form-control" placeholder="Neues Passwort" disabled>
                            </div>
                            <!-- Gekündigt -->
<div class="form-group">
    <strong><i class="fas fa-user-times mr-1"></i> Gekuendigt</strong> 
    <div class="form-check">
        <input type="checkbox" id="gekuendigtCheckbox" class="form-check-input" name="gekuendigt" <?php echo $user['gekuendigt'] === 'gekuendigt' ? 'checked' : ''; ?>>
        <label for="gekuendigtCheckbox" class="form-check-label">Benutzer als gekuendigt markieren</label>
    </div>
</div>

<?php if ($user['bewerber'] === 'ja'): ?>
    <!-- Bewerber -->
    <div class="form-group">
        <strong><i class="fas fa-user-times mr-1"></i> Bewerber</strong>
        <div class="form-check">
            <input type="checkbox" id="bewerberCheckbox" class="form-check-input" name="bewerber" <?php echo $user['bewerber'] === 'ja' ? 'checked' : ''; ?>>
            <label for="bewerberCheckbox" class="form-check-label">Bewerber (Checkbox = User ist Bewerber / Checkbox uncheck = User wurde angenommen)</label>
        </div>
    </div>
<?php endif; ?>

                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
                    <button type="submit" class="btn btn-primary" id="saveChanges">Speichern</button>
                </div>
            </div>
        </div>
    </div>
</form>


<script>
    // Event-Listener für die Checkbox
    document.getElementById('changePasswordCheckbox').addEventListener('change', function() {
        // Hole das Passwortfeld
        var passwordField = document.getElementById('passwordField');
        
        // Wenn die Checkbox aktiviert ist, setze 'disabled' auf false (also aktivieren)
        if (this.checked) {
            passwordField.disabled = false;
        } else {
            // Wenn die Checkbox deaktiviert ist, setze 'disabled' auf true (also deaktivieren)
            passwordField.disabled = true;
        }
    });
    $(document).ready(function () {
        $("#saveChanges").on("click", function (e) {
            e.preventDefault();

            // Sammle die Daten aus dem Formular
            var formData = $("#userEditForm").serialize();

            // Sende die Daten per AJAX
            $.ajax({
                url: "include/edit_user.php",
                type: "POST",
                data: formData,
                success: function(response) {
                    try {
                        // Versuche, die Antwort als JSON zu parsen
                        response = JSON.parse(response);  // Hier die Antwort als JSON parsen

                        if (response.success) {
                            alert('Daten erfolgreich gespeichert');
                            $('#user-bearbeiten').modal('hide'); // Schließt das Modal
                            location.reload();  // Lädt die Seite neu
                        } else {
                            alert('Fehler: ' + response.message);  // Fehlernachricht anzeigen
                        }
                    } catch (error) {
                        console.error("Fehler beim Parsen der Antwort:", error);
                        alert("Fehler beim Parsen der Antwort");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX-Fehler:", error);
                }
            });
        });
    });
</script>

          <div class="col-md-9">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#dokumente" data-toggle="tab">Dokumente</a></li>
                  <?php if ($_SESSION['permissions']['view_employee_notes'] ?? false): ?>
                  <li class="nav-item"><a class="nav-link" href="#notizen" data-toggle="tab">Notizen</a></li>
                  <?php endif; ?>
                  <?php
                    // Überprüfen, ob der Benutzer als Bewerber markiert ist
                    if (($user['bewerber'] !== 'ja') && ($_SESSION['permissions']['view_employee_ausbildungen'] ?? false)) {
                        echo '<li class="nav-item"><a class="nav-link" href="#ausbildungen" data-toggle="tab">Ausbildungen</a></li>';
                    }

                    if (($user['bewerber'] !== 'ja') && ($_SESSION['permissions']['view_employee_ausruestung'] ?? false)) {
                        echo '<li class="nav-item"><a class="nav-link" href="#ausruestung" data-toggle="tab">Ausrüstung</a></li>';
                    }
                    ?>
                </ul>
              </div>
              <div class="card-body">
                <div class="tab-content">
                  <!-- Dokumente -->
                  <div class="active tab-pane" id="dokumente">
                  <form id="employeeForm" class="form-horizontal" method="POST">
    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id); ?>">
    <?php
// Führerscheine aus der Datenbank abrufen
$sql = "SELECT fuehrerscheine FROM employee_info WHERE user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$fuehrerscheine = json_decode($result['fuehrerscheine'] ?? '[]', true);

if (!is_array($fuehrerscheine)) {
    $fuehrerscheine = []; // Falls JSON fehlerhaft ist, Standardwert setzen
}

// Waffenschein aus der Datenbank abrufen
$sql = "SELECT waffenschein_type FROM employee_info WHERE user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$waffenschein_type = $result['waffenschein_type'] ?? 'none'; // Standardwert 'none', falls nicht gesetzt

?>
    <!-- Waffenschein -->
    <div class="form-group row">
    <label for="waffenscheinSelect" class="col-sm-2 col-form-label">Waffenschein</label>
    <div class="col-sm-10">
        <select id="waffenscheinSelect" class="form-control" name="waffenschein_type" 
                <?= isset($_SESSION['permissions']['edit_waffenschein']) && $_SESSION['permissions']['edit_waffenschein'] ? '' : 'disabled'; ?>>
            <option value="none" <?= $waffenschein_type === 'none' ? 'selected' : ''; ?>>Keiner Vorhanden</option>
            <option value="small" <?= $waffenschein_type === 'small' ? 'selected' : ''; ?>>Kleiner Waffenschein</option>
            <option value="big_small" <?= $waffenschein_type === 'big_small' ? 'selected' : ''; ?>>Großer & Kleiner Waffenschein</option>
        </select>
    </div>
</div>



<!-- Führerscheine -->
<div class="form-group row">
    <label for="fuehrerscheinSelect" class="col-sm-2 col-form-label">Führerscheine</label>
    <div class="col-sm-10">
        <select id="fuehrerscheinSelect" class="form-control" multiple name="fuehrerscheine[]"
                <?= isset($_SESSION['permissions']['edit_fuehrerscheine']) && $_SESSION['permissions']['edit_fuehrerscheine'] ? '' : 'disabled'; ?>>
            <option value="C" <?= in_array('C', $fuehrerscheine) ? 'selected' : ''; ?>>C</option>
            <option value="A" <?= in_array('A', $fuehrerscheine) ? 'selected' : ''; ?>>A</option>
            <option value="M2" <?= in_array('M2', $fuehrerscheine) ? 'selected' : ''; ?>>M2</option>
            <option value="PTL" <?= in_array('PTL', $fuehrerscheine) ? 'selected' : ''; ?>>PTL</option>
        </select>
    </div>
</div>

        <!-- Weitere Dokumente -->
        <script>
$(document).ready(function () {
    // Speichern-Button
    $("#saveButton").on("click", function () {
    var formData = $("#employeeForm").serialize();

    $.ajax({
        url: "include/save_employee_info.php",
        type: "POST",
        data: formData,
        success: function (response) {
            try {
                var res = JSON.parse(response); // JSON-Antwort parsen
                if (res.success) {
                } else {
                }
            } catch (e) {
                console.error("Fehler beim Verarbeiten der Antwort:", e);
            }
        },
        error: function (xhr, status, error) {
            console.error("Fehler:", error);
        },
    });
});
    // Datei-Auswahl anzeigen
    $("#documentFile").on("change", function () {
        var fileName = $(this).val().split("\\").pop();
        $(this).next(".custom-file-label").text(fileName);
    });
});
</script>        
<!-- Button für das Modal -->
<?php if ($_SESSION['permissions']['upload_file'] ?? false): ?>

          <div class="form-group row">
              <label for="uploadButton" class="col-sm-2 col-form-label">Dokumente Hochladen</label>
              <div class="col-sm-10">
                  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-primary">
                      Dokument hochladen
                  </button>
              </div>
          </div>
          <?php endif; ?>

    </form>

<!-- Modal für Dateiupload -->
<div class="modal fade" id="modal-primary">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <?php if ($_SESSION['permissions']['upload_file'] ?? false): ?>
                        Datei hochladen
                    <?php else: ?>
                        Keine Berechtigung
                    <?php endif; ?>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <?php if ($_SESSION['permissions']['upload_file'] ?? false): ?>
                <!-- Formular nur anzeigen, wenn Berechtigung vorhanden -->
                <form id="uploadForm" action="include/upload_document.php" method="POST" enctype="multipart/form-data">
                    <!-- Benutzer-ID -->
                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id); ?>">
                    <!-- Dokumenttyp -->
                    <input type="hidden" name="doc_type" value="arbeitsvertrag"> <!-- Beispiel für den Dokumenttyp -->

                    <div class="modal-body">
                        <!-- Eingabe für den benutzerdefinierten Namen -->
                        <div class="form-group">
                            <label for="documentName">Dokumentname</label>
                            <input type="text" id="documentName" name="document_name" class="form-control" placeholder="z.B. Arbeitsvertrag" required>
                        </div>

                        <!-- Dateiupload -->
                        <div class="form-group">
                            <label for="documentFile">Datei auswählen</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="documentFile" name="document_file" required>
                                <label class="custom-file-label" for="documentFile">Datei auswählen</label>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                        <button type="submit" class="btn btn-primary">Hochladen</button>
                    </div>
                </form>
            <?php else: ?>
                <!-- Nachricht für Benutzer ohne Berechtigung -->
                <div class="modal-body">
                    <p>Sie haben keine Berechtigung, Dateien hochzuladen.</p>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
                </div>
            <?php endif; ?>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<script>
    document.getElementById("documentFile").addEventListener("change", function() {
        var fileName = this.files[0].name;
        var nextSibling = this.nextElementSibling;
        nextSibling.innerText = fileName; // Ändert den Text des Labels
    });
</script>
<script>
    $(document).ready(function() {
    $("#uploadForm").on("submit", function(e) {
        e.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            url: $(this).attr("action"),
            type: $(this).attr("method"),
            data: formData,
            processData: false, // Wichtig für FormData
            contentType: false, // Wichtig für FormData
            success: function(response) {
                $("#modal-primary").modal("hide"); // Modal schließen
                location.reload(); // Seite neu laden, um Änderungen zu sehen
            },
            error: function(xhr, status, error) {
            }
        });
    });
});
</script>


<!-- /.modal -->

    <!-- Liste der hochgeladenen Dokumente -->
    <div class="mt-4">
    <h5>Hochgeladene Dokumente:</h5>
    <?php if ($_SESSION['permissions']['view_documents'] ?? false): ?>
        <ul>
            <?php if (!empty($documents)): ?>
                <?php foreach ($documents as $doc): ?>
                    <li>
                        <a href="<?= htmlspecialchars($doc['file_path']); ?>" target="_blank">
                            <?= htmlspecialchars($doc['file_name']); ?>
                        </a> 
                        (<?= htmlspecialchars($doc['uploaded_at']); ?>)
                        
                        <!-- Löschen-Button, nur wenn der Benutzer die Berechtigung hat -->
                        <?php if ($_SESSION['permissions']['delete_documents'] ?? false): ?>
                            <button class='btn btn-danger btn-sm delete-document' data-id='<?php echo $doc['id']; ?>'>
                                <i class='fas fa-trash'></i> 
                            </button>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>Keine Dokumente vorhanden.</li>
            <?php endif; ?>
        </ul>
    <?php else: ?>
        <p>Sie haben keine Berechtigung, die hochgeladenen Dokumente anzuzeigen.</p>
    <?php endif; ?>
</div>
<script>
$(document).ready(function() {
    // Event-Listener für den Delete-Button
    $('.delete-document').on('click', function() {
        var documentId = $(this).data('id'); // Holen der Dokument-ID aus dem data-id Attribut
        console.log("Document ID: ", documentId); // Debugging-Zeile

        // Bestätigungsdialog
        if (confirm("Möchten Sie dieses Dokument wirklich löschen?")) {
            // AJAX-Anfrage zum Löschen des Dokuments
            $.ajax({
                url: 'include/delete_document.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    document_id: documentId
                },
                success: function(response) {
                    if (response.success) {
                        // Erfolgsnachricht und das Dokument aus der Anzeige entfernen
                        alert(response.message);
                        // Entferne das Dokument aus der Liste
                        $('button[data-id="'+documentId+'"]').closest('li').fadeOut();
                    } else {
                        alert(response.message); // Fehlermeldung anzeigen
                    }
                },
                error: function(xhr, status, error) {
                    alert('Es gab einen Fehler beim Löschen des Dokuments.');
                }
            });
        }
    });
});
</script>

</div>

<!-- Tab für Notizen -->
<div class="tab-pane" id="notizen">
    <?php if ($_SESSION['permissions']['create_note'] ?? false || $_SESSION['permissions']['create_warning'] ?? false || $_SESSION['permissions']['create_termination'] ?? false): ?>
        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#modal-default">Notiz hinzufügen</button>
    <?php else: ?>
        <p class="text-muted">Sie haben keine Berechtigung, Notizen hinzuzufügen.</p>
    <?php endif; ?>

    <div id="timeline" class="timeline timeline-inverse">
        <?php foreach ($notes as $note): ?>
            <div>
                <?php
                $iconClass = 'fas fa-user bg-secondary';
                if ($note['type'] === 'notiz') {
                    $iconClass = 'fas fa-user bg-info';
                } elseif ($note['type'] === 'verwarnung') {
                    $iconClass = 'fas fa-user bg-warning';
                } elseif ($note['type'] === 'kuendigung') {
                    $iconClass = 'fas fa-user bg-danger';
                }
                ?>
                <i class="<?= $iconClass; ?>"></i>
                <div class="timeline-item">
                    <span class="time"><i class="far fa-clock"></i> <?= htmlspecialchars($note['created_at']); ?></span>
                    <h3 class="timeline-header"><?= htmlspecialchars($note['author'] ?? 'Unbekannt'); ?> fügte eine <?= htmlspecialchars($note['type']); ?> hinzu</h3>
                    <div class="timeline-body"><?= htmlspecialchars($note['content']); ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal für Notizerstellung -->
<div class="modal fade" id="modal-default">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Notiz erstellen</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="noteForm">
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id); ?>"> <!-- Benutzer-ID -->
                <div class="modal-body">
                    <div class="form-group">
                        <label>Typ</label>
                        <select id="noteType" name="note_type" class="form-control" required>
                            <?php if ($_SESSION['permissions']['create_note'] ?? false): ?>
                                <option value="notiz">Notiz</option>
                            <?php endif; ?>
                            <?php if ($_SESSION['permissions']['create_warning'] ?? false): ?>
                                <option value="verwarnung">Verwarnung</option>
                            <?php endif; ?>
                            <?php if ($_SESSION['permissions']['create_termination'] ?? false): ?>
                                <option value="kuendigung">Kündigung</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Inhalt</label>
                        <textarea id="noteContent" name="note_content" class="form-control" rows="3" placeholder="Enter ..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Abbrechen</button>
                    <button type="submit" class="btn btn-primary">Speichern</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$("#noteForm").on("submit", function (e) {
    e.preventDefault();
    var formData = $(this).serialize();

    $.ajax({
        url: "include/add_note.php",
        type: "POST",
        data: formData,
        success: function (response) {
            try {
                response = JSON.parse(response);

                if (response.success) {
                  $('#modal-default').modal('hide');
                  $('.modal-backdrop').remove(); // Entfernt den dunklen Hintergrund
                    $("#noteForm")[0].reset();

                    var iconClass;
                    switch (response.data.type) {
                        case "notiz":
                            iconClass = "fas fa-user bg-info";
                            break;
                        case "verwarnung":
                            iconClass = "fas fa-user bg-warning";
                            break;
                        case "kuendigung":
                            iconClass = "fas fa-user bg-danger";
                            break;
                        default:
                            iconClass = "fas fa-user bg-secondary";
                            break;
                    }

                    var newNote = `
                        <div>
                            <i class="${iconClass}"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="far fa-clock"></i> ${response.data.created_at}</span>
                                <h3 class="timeline-header">${response.data.author} fügte eine ${response.data.type} hinzu</h3>
                                <div class="timeline-body">${response.data.content}</div>
                            </div>
                        </div>`;
                    $("#timeline").prepend(newNote);
                } else {
                    alert(response.message);
                }
            } catch (error) {
                console.error("Fehler beim Parsen der Antwort:", error);
            }
        },
        error: function (xhr, status, error) {
            console.error("Fehler:", error);
            alert("Es ist ein Fehler aufgetreten: " + error);
        },
    });
});
</script>


                  <!-- Ausbildungen -->
                  <div class="tab-pane" id="ausbildungen">
    <form id="ausbildungForm">
        <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id); ?>">
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Bewertungen</label>
            <div class="col-sm-10">
                <?php
                // Ausbildungstypen aus der Datenbank abrufen
                $stmt = $conn->prepare("SELECT key_name, display_name FROM ausbildungstypen");
                $stmt->execute();
                $ausbildungstypen = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Benutzerausbildungen abrufen
                $stmt = $conn->prepare("SELECT ausbildung, status, bewertung FROM ausbildungen WHERE user_id = :user_id");
                $stmt->execute([':user_id' => $user_id]);
                $ausbildungen = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Benutzerausbildungen in ein Array umwandeln
                $dbAusbildungen = [];
                foreach ($ausbildungen as $ausbildung) {
                    $dbAusbildungen[$ausbildung['ausbildung']] = [
                        'status' => (int)$ausbildung['status'],
                        'bewertung' => (int)$ausbildung['bewertung']
                    ];
                }

                // Darstellung der Ausbildungstypen und der Benutzerausbildungen
                foreach ($ausbildungstypen as $type) {
                    $keyName = $type['key_name'];
                    $displayName = $type['display_name'];
                    $status = $dbAusbildungen[$keyName]['status'] ?? 0;
                    $rating = $dbAusbildungen[$keyName]['bewertung'] ?? 0;
                    ?>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" 
                               id="<?= htmlspecialchars($keyName); ?>" 
                               name="ausbildungen[<?= htmlspecialchars($keyName); ?>][status]" 
                               value="1" <?= $status ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="<?= htmlspecialchars($keyName); ?>">
                            <?= htmlspecialchars($displayName); ?>
                        </label>
                        <div class="stars ml-3" data-rating="<?= $rating; ?>" data-id="<?= htmlspecialchars($keyName); ?>">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="<?= $i <= $rating ? 'fas' : 'far'; ?> fa-star" 
                                   data-value="<?= $i; ?>" 
                                   data-ausbildung="<?= htmlspecialchars($keyName); ?>"></i>
                            <?php endfor; ?>
                            <input type="hidden" name="ausbildungen[<?= htmlspecialchars($keyName); ?>][rating]" value="<?= $rating; ?>">
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </form>
</div>




<script>
    $(document).ready(function () {
    $(".stars i").on("click", function () {
        var rating = $(this).data("value"); // Wert des angeklickten Sterns
        var ausbildungId = $(this).data("ausbildung"); // ID der Ausbildung

        // Sterne setzen
        $(`.stars[data-id="${ausbildungId}"] i`).each(function () {
            if ($(this).data("value") <= rating) {
                $(this).removeClass("far").addClass("fas");
            } else {
                $(this).removeClass("fas").addClass("far");
            }
        });

        // Hidden-Input aktualisieren
        $(`.stars[data-id="${ausbildungId}"] input[name="ausbildungen[${ausbildungId}][rating]"]`).val(rating);
    });

    // Speichern
    $("#saveButton").on("click", function () {
        var formData = $("#ausbildungForm").serialize();

        $.ajax({
            url: "include/save_ausbildungen.php",
            type: "POST",
            data: formData,
            success: function (response) {
                try {
                    response = JSON.parse(response);
                    if (response.success) {
                        alert("Änderungen erfolgreich gespeichert.");
                        location.reload(); // Seite neu laden
                    } else {
                        alert("Fehler: " + response.message);
                    }
                } catch (error) {
                    console.error("Fehler beim Parsen der Antwort:", error);
                }
            },
            error: function (xhr, status, error) {
                console.error("Fehler:", error);
                alert("Fehler beim Speichern.");
            }
        });
    });
});

</script>


<!-- Ausrüstung -->
<div class="tab-pane" id="ausruestung">
    <form id="ausruestungForm">
        <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id); ?>">
        <input type="hidden" name="user_name" value="<?= htmlspecialchars($user_name); ?>"> <!-- Hidden input für user_name -->

        <?php
        // Deine Datenbankverbindung einbinden
        include 'include/db.php';

        // Einbinden der Ausrüstungslogik
        include 'include/ausruestung.php';

        // Die Rückgabe von `ausruestung.php` in Variablen speichern
        $data = include 'include/ausruestung.php';

        // Zugriff auf die Variablen aus der `ausruestung.php`
        $canEdit = $data['canEdit'];
        $categories = $data['categories'];
        $userAusrüstung = $data['userAusrüstung'];
        $letzte_spind_kontrolle = $data['letzte_spind_kontrolle'];
        $notizen = $data['notizen'];
        ?>

        <?php
        // HTML-Ausgabe der Ausrüstungs-Kategorien
        foreach ($categories as $category => $items) {
        ?>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label"><?= htmlspecialchars($category); ?></label>
                <div class="col-sm-10">
                    <?php foreach ($items as $item):
                        $status = $userAusrüstung[$item['key_name']] ?? 0;
                    ?>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" 
                                   id="<?= $item['key_name']; ?>" 
                                   name="ausruestung[<?= $item['key_name']; ?>]" 
                                   value="1" <?= $status ? 'checked' : ''; ?>
                                   <?= $canEdit ? '' : 'disabled'; ?>>
                            <label class="form-check-label" for="<?= $item['key_name']; ?>">
                                <?= htmlspecialchars($item['display_name']); ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php
        }
        ?>

        <div class="form-group">
            <label for="letzteSpindKontrolle">Letzte Spind Kontrolle</label>
            <input type="date" class="form-control" id="letzteSpindKontrolle" name="letzte_spind_kontrolle" 
                   value="<?= htmlspecialchars($letzte_spind_kontrolle ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="notiz">Notiz</label>
            <input type="text" class="form-control" id="notiz" name="notiz" value="<?= htmlspecialchars($notizen ?? ''); ?>">
        </div>

        <button type="button" id="saveAusruestungButton" class="btn btn-primary">Änderungen Speichern</button>
    </form>
</div>

<script>
    document.getElementById('saveAusruestungButton').addEventListener('click', function() {
    // Alle nicht aktivierten Checkboxen auf "0" setzen
    var checkboxes = document.querySelectorAll('.form-check-input');
    checkboxes.forEach(function(checkbox) {
        if (!checkbox.checked) {
            // Wenn die Checkbox nicht aktiviert ist, setze den Wert auf "0"
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ausruestung[' + checkbox.id + ']';
            input.value = '0';
            document.getElementById('ausruestungForm').appendChild(input);
        }
    });

    // Daten aus den Eingabefeldern "Letzte Spind Kontrolle" und "Notiz" holen
    var letzteSpindKontrolle = document.getElementById('letzteSpindKontrolle').value;
    var notiz = document.getElementById('notiz').value;

    // Formulardaten sammeln
    var formData = new FormData(document.getElementById('ausruestungForm'));
    formData.append('letzte_spind_kontrolle', letzteSpindKontrolle);
    formData.append('notizen', notiz);

    // Debugging: Ausgabe der Formulardaten
    console.log("Formulardaten:", formData);

    // AJAX-Anfrage starten
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'include/save_ausruestung.php', true);

    // Response-Handler definieren
    xhr.onload = function() {
        if (xhr.status === 200) {
            console.log(xhr.responseText); // Ausgabe der Antwort im Browser
            try {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    alert('Änderungen wurden erfolgreich gespeichert!');
                } else {
                    alert('Fehler beim Speichern: ' + response.message);
                }
            } catch (e) {
                alert('Fehler beim Parsen der Antwort!');
                console.error(e);
            }
        } else {
            alert('Fehler beim Speichern: ' + xhr.status);
        }
    };

    // Fehler im Request
    xhr.onerror = function() {
        alert('Fehler beim Senden der Anfrage!');
    };

    // Anfrage absenden
    xhr.send(formData);
});
</script>




                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 3.2.0
    </div>
    <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
  </footer>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>