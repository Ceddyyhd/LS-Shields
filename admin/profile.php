<!DOCTYPE html>
<html lang="en">
<?php include 'include/header.php'; ?>

<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <?php include 'include/navbar.php'; ?>
  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <h1>Profile</h1>
      </div>
    </section>
    <section class="content">
      <div class="container-fluid">
        <!-- Profil Details -->
        <div class="card">
          <div class="card-header bg-primary">
            <h3 class="card-title">Information</h3>
          </div>
          <div class="card-body">
            <p><strong>Gmail:</strong> example@gmail.com</p>
            <p><strong>UMail:</strong> example@umail.com</p>
            <p><strong>Kontonummer:</strong> 1234567890</p>
            <p><strong>Letzte Beförderung durch:</strong> Admin</p>
          </div>
        </div>

        <!-- Dokumente -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Dokumente</h3>
          </div>
          <div class="card-body">
            <form action="upload_document.php" method="POST" enctype="multipart/form-data">
              <div class="form-group">
                <label for="file">Datei hochladen:</label>
                <input type="file" name="file" class="form-control" required>
              </div>
              <button type="submit" class="btn btn-primary">Hochladen</button>
            </form>
            <hr>
            <h4>Hochgeladene Dokumente:</h4>
            <?php
            $user_id = $_SESSION['user_id'];
            $documents = $db->query("SELECT * FROM documents WHERE user_id = $user_id")->fetchAll();
            foreach ($documents as $doc) {
                echo "<p><a href='{$doc['file_path']}'>{$doc['file_name']}</a></p>";
            }
            ?>
          </div>
        </div>

        <!-- Notizen -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Notizen</h3>
          </div>
          <div class="card-body">
            <button class="btn btn-success" data-toggle="modal" data-target="#addNoteModal">Notiz hinzufügen</button>
            <hr>
            <h4>Notizen:</h4>
            <?php
            $notes = $db->query("SELECT * FROM notes WHERE user_id = $user_id")->fetchAll();
            foreach ($notes as $note) {
                echo "<p>{$note['note']} <small>am {$note['created_at']}</small></p>";
            }
            ?>
          </div>
        </div>

        <!-- Modal für Notizen -->
        <div class="modal fade" id="addNoteModal" tabindex="-1" role="dialog">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Notiz hinzufügen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form action="add_note.php" method="POST">
                <div class="modal-body">
                  <textarea name="note" class="form-control" rows="5" required></textarea>
                </div>
                <div class="modal-footer">
                  <button type="submit" class="btn btn-primary">Speichern</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Ausbildungen -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Ausbildungen</h3>
          </div>
          <div class="card-body">
            <form action="save_training.php" method="POST">
              <div class="form-group">
                <label>Java:</label>
                <input type="checkbox" name="trainings[java][completed]"> Abgeschlossen
                <input type="number" name="trainings[java][rating]" min="1" max="5" placeholder="Bewertung">
              </div>
              <button type="submit" class="btn btn-primary">Speichern</button>
            </form>
          </div>
        </div>

        <!-- Ausrüstung -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Ausrüstung</h3>
          </div>
          <div class="card-body">
            <form action="save_equipment.php" method="POST">
              <div class="form-group">
                <label>Laptop:</label>
                <input type="checkbox" name="equipment[laptop]"> Erhalten
              </div>
              <button type="submit" class="btn btn-primary">Speichern</button>
            </form>
          </div>
        </div>

      </div>
    </section>
  </div>
</div>
<?php include 'include/footer.php'; ?>
<script src="path/to/js/framework.js"></script>
</body>
</html>
