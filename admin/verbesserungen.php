<!DOCTYPE html>
<html lang="en">
<?php include 'include/header.php'; ?>

<body class="hold-transition sidebar-mini">
<div class="wrapper">

<?php include 'include/navbar.php'; ?>
<?php
include 'include/db.php'; // Datenbankverbindung
session_start(); // Sitzung starten

// Abruf aller Vorschläge aus der Datenbank
$query = "SELECT * FROM verbesserungsvorschlaege ORDER BY datum_uhrzeit DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$vorschlaege = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

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
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-vorschlag-create">
                  Anfrage erstellen
                </button>      
              </div>
              <!-- Verbesserungsvorschlag erstellen Modal -->
              <div class="modal fade" id="modal-vorschlag-create">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h4 class="modal-title">Neuen Verbesserungsvorschlag erstellen</h4>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <form id="createSuggestionForm">
                        <div class="form-group">
                          <label>Bereich</label>
                          <select class="custom-select" name="bereich">
                            <option value="Personal">Personal</option>
                            <option value="Ausrüstung">Ausrüstung</option>
                            <option value="Ausbildung">Ausbildung</option>
                            <option value="IT">IT</option>
                            <option value="Sonstiges">Sonstiges</option>
                          </select>
                        </div>
                        <div class="form-group">
                          <div class="form-check">
                            <input type="checkbox" id="anonym" class="form-check-input" name="fuel_checked" value="true">
                            <label for="anonym">Anonym (Aktiviert = kein Name mitsenden)</label>
                          </div>
                        </div>
                        <div class="form-group">
                          <label for="betreff">Betreff</label>
                          <input type="text" name="betreff" id="betreff" class="form-control" placeholder="Betreff eingeben" required>
                        </div>
                        <div class="form-group">
                          <label for="vorschlag">Vorschlag</label>
                          <textarea name="vorschlag" id="vorschlag" class="form-control" rows="4" placeholder="Beschreiben Sie den Vorschlag" required></textarea>
                        </div>
                      </form>
                    </div>
                    <div class="modal-footer justify-content-between">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
                      <button type="button" class="btn btn-primary" id="saveRequestBtn">Speichern</button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- End Verbesserungsvorschlag erstellen Modal -->

              <!-- Vorschläge Tabelle -->
              <div class="card-body">
                <table class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Bereich</th>
                      <th>Vorschlag</th>
                      <th>Betreff</th>
                      <th>Datum & Uhrzeit</th>
                      <th>Status</th>
                      <th>Details einblenden</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($vorschlaege as $vorschlag): ?>
                    <tr>
                      <td><?= htmlspecialchars($vorschlag['id']) ?></td>
                      <td><?= mb_strimwidth(htmlspecialchars($vorschlag['vorschlag']), 0, 50, '...') ?></td>
                      <td><?= mb_strimwidth(htmlspecialchars($vorschlag['betreff']), 0, 25, '...') ?></td>
                      <td><?= htmlspecialchars($vorschlag['datum_uhrzeit']) ?></td>
                      <td><?= htmlspecialchars($vorschlag['status']) ?></td>
                      <td>
                        <button class="btn btn-info btn-sm" 
                                data-toggle="modal" 
                                data-target="#modal-vorschlag-bearbeiten"
                                data-id="<?= $vorschlag['id'] ?>">
                            Anfrage bearbeiten
                        </button>              
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Verbesserungsvorschlag bearbeiten Modal -->
    <?php
    // Sicherstellen, dass eine ID übergeben wurde
    $vorschlag = null; 
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $vorschlagId = $_GET['id'];

        // SQL-Abfrage, um nur den spezifischen Vorschlag zu holen
        $query = "SELECT * FROM verbesserungsvorschlaege WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $vorschlagId, PDO::PARAM_INT); // Bindet die ID sicher
        $stmt->execute();
        $vorschlag = $stmt->fetch(PDO::FETCH_ASSOC);

        // Überprüfen, ob der Vorschlag existiert
        if (!$vorschlag) {
            echo "Vorschlag nicht gefunden!";
            exit; // Abbruch, wenn kein Vorschlag gefunden wurde
        }
    }
    ?>
    <?php if ($vorschlag): ?>
    <div class="modal fade" id="modal-vorschlag-bearbeiten">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalLabel">Vorschlag bearbeiten</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="editSuggestionForm">
              <!-- Bereich -->
              <div class="form-group">
                <label for="bereich">Bereich</label>
                <select class="custom-select" name="bereich" id="bereich">
                  <option value="Personal" <?php echo ($vorschlag['bereich'] == 'Personal') ? 'selected' : ''; ?>>Personal</option>
                  <option value="Ausrüstung" <?php echo ($vorschlag['bereich'] == 'Ausrüstung') ? 'selected' : ''; ?>>Ausrüstung</option>
                  <option value="Ausbildung" <?php echo ($vorschlag['bereich'] == 'Ausbildung') ? 'selected' : ''; ?>>Ausbildung</option>
                  <option value="IT" <?php echo ($vorschlag['bereich'] == 'IT') ? 'selected' : ''; ?>>IT</option>
                  <option value="Sonstiges" <?php echo ($vorschlag['bereich'] == 'Sonstiges') ? 'selected' : ''; ?>>Sonstiges</option>
                </select>
              </div>

              <!-- Anonym Checkbox -->
              <div class="form-group">
                <div class="form-check">
                  <input type="checkbox" id="anonym" class="form-check-input" name="fuel_checked" <?php echo ($vorschlag['anonym'] == '1') ? 'checked' : ''; ?>>
                  <label for="anonym">Anonym (Aktiviert = kein Name mitsenden)</label>
                </div>
              </div>

              <!-- Betreff -->
              <div class="form-group">
                <label for="betreff">Betreff</label>
                <input type="text" name="betreff" id="betreff" class="form-control" value="<?php echo htmlspecialchars($vorschlag['betreff']); ?>">
              </div>

              <!-- Vorschlag -->
              <div class="form-group">
                <label for="vorschlag">Vorschlag</label>
                <textarea name="vorschlag" id="vorschlag" class="form-control"><?php echo htmlspecialchars($vorschlag['vorschlag']); ?></textarea>
              </div>

              <!-- Status -->
              <div class="form-group">
                <label for="status">Status</label>
                <select class="custom-select" name="status" id="status">
                  <option value="Angefragt" <?php echo ($vorschlag['status'] == 'Angefragt') ? 'selected' : ''; ?>>Angefragt</option>
                  <option value="in Bearbeitung" <?php echo ($vorschlag['status'] == 'in Bearbeitung') ? 'selected' : ''; ?>>in Bearbeitung</option>
                  <option value="Rückfragen" <?php echo ($vorschlag['status'] == 'Rückfragen') ? 'selected' : ''; ?>>Rückfragen</option>
                  <option value="Angenommen" <?php echo ($vorschlag['status'] == 'Angenommen') ? 'selected' : ''; ?>>Angenommen</option>
                  <option value="Abgelehnt" <?php echo ($vorschlag['status'] == 'Abgelehnt') ? 'selected' : ''; ?>>Abgelehnt</option>
                </select>
              </div>

              <!-- Notiz -->
              <div class="form-group">
                <label for="notiz">Notiz</label>
                <textarea name="notiz" id="notiz" class="form-control"><?php echo htmlspecialchars($vorschlag['notiz']); ?></textarea>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
            <button type="button" class="btn btn-primary" id="saveEditBtn">Speichern</button>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

  </div>
  <!-- /.content-wrapper -->

  <footer class="main-footer">
    <div class="float-right d-none d-sm-inline">
      Anything you want
    </div>
    <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>
