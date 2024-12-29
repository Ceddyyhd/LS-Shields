<?php
include 'include/db.php';
ini_set('display_errors', 0);
error_reporting(0);

// Kunden-ID aus der URL holen (z.B. kunden.php?id=1)
$kunden_id = $_GET['id'] ?? null;
if (!$kunden_id) {
    die("Kunden-ID fehlt.");
}

// Kundeninformationen abrufen
$sql = "SELECT * FROM Kunden WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute(['id' => $kunden_id]);
$kunde = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kunde) {
    die("Kunde nicht gefunden.");
}

?>

<!DOCTYPE html>
<html lang="en">
<?php include 'include/header.php'; ?>

<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <?php include 'include/navbar.php'; ?>

  <!-- Main content -->
  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Kundenprofil</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Kundenprofil</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">
            <!-- Profil Bild -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle" src="path/to/default/image.jpg" alt="Kundenprofil Bild">
                </div>

                <h3 class="profile-username text-center"><?php echo htmlspecialchars($kunde['name']); ?></h3>
                <p class="text-muted text-center"><?php echo htmlspecialchars($kunde['umail']); ?></p>

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>Telefonnummer:</b> <a class="float-right"><?php echo htmlspecialchars($kunde['nummer']); ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>Erstellt am:</b> <a class="float-right"><?php echo htmlspecialchars($kunde['created_at']); ?></a>
                  </li>
                </ul>

                <button type="button" class="btn btn-primary" onclick="window.location.href='kunden_edit.php?id=<?php echo $kunde['id']; ?>'">Kunde Bearbeiten</button>
              </div>
            </div>
          </div>

          <!-- Kundendetails -->
          <div class="col-md-9">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#dokumente" data-toggle="tab">Dokumente</a></li>
                  <li class="nav-item"><a class="nav-link" href="#notizen" data-toggle="tab">Notizen</a></li>
                  <li class="nav-item"><a class="nav-link" href="#ausruestung" data-toggle="tab">Ausrüstung</a></li>
                </ul>
              </div>
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="dokumente">
                    <!-- Dokumente Anzeigen (Beispiel) -->
                    <ul>
                      <li>Dokument 1</li>
                      <li>Dokument 2</li>
                    </ul>
                  </div>

                  <div class="tab-pane" id="notizen">
                    <!-- Notizen Anzeigen -->
                    <p>Notizen zum Kunden</p>
                  </div>

                  <div class="tab-pane" id="ausruestung">
                    <!-- Ausrüstung Anzeigen -->
                    <p>Ausrüstung des Kunden</p>
                  </div>
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
