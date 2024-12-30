<?php
include 'include/db.php';
ini_set('display_errors', 0);
error_reporting(0);

// Beispiel: Kunden-ID aus der Session oder URL (z. B. profile.php?id=1)
$customer_id = $_GET['id'] ?? null;
if (!$customer_id) {
    die("Kunden-ID fehlt.");
}

// Kundeninformationen abrufen
$sql = "SELECT k.* FROM kunden k WHERE k.id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute(['id' => $customer_id]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    die("Kunde nicht gefunden.");
}

// Dokumente für den Kunden abrufen
$sql_documents = "SELECT file_name, file_path, uploaded_at FROM documents_customer WHERE user_id = :user_id";
$stmt_documents = $conn->prepare($sql_documents);
$stmt_documents->execute(['user_id' => $customer_id]);
$documents = $stmt_documents->fetchAll(PDO::FETCH_ASSOC);

// Rechte des Benutzers abrufen
$sql_permissions = "SELECT p.name, p.description, p.display_name 
                    FROM permissions p
                    JOIN roles r ON r.id = :role_id";
$stmt_permissions = $conn->prepare($sql_permissions);
$stmt_permissions->execute(['role_id' => $customer['role_id']]);
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
                  <img class="profile-user-img img-fluid img-circle" src="<?php echo htmlspecialchars($customer['profile_image']); ?>" alt="User profile picture">
                </div>

                <h3 class="profile-username text-center">
                  <?php echo htmlspecialchars($customer['name']); ?>
                </h3>

                <ul class="list-group list-group-unbordered mb-3">
                <li class="list-group-item">
                    <b>Tel. Nr.:</b> <a class="float-right"><?php echo htmlspecialchars($customer['nummer']); ?></a>
                </li>
                  <li class="list-group-item">
                    <b>Erstellt am:</b> <a class="float-right">
                      <?php echo htmlspecialchars($customer['created_at']); ?>
                    </a>
                  </li>
                </ul>
              </div>
            </div>

        <!-- About Me Box -->
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Information</h3>
          </div>
          <div class="card-body">
            <strong><i class="fas fa-envelope mr-1"></i> UMail</strong>
            <p class="text-muted"><?php echo htmlspecialchars($customer['umail']); ?></p>

            <hr>
            <strong><i class="fas fa-credit-card mr-1"></i> Kontonummer</strong>
            <p class="text-muted"><?php echo htmlspecialchars($customer['kontonummer']); ?></p>
          </div>

          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#user-bearbeiten">
                  Kunden Bearbeiten
          </button>
        </div>
      </div>
      <form id="userEditForm">
    <input type="hidden" name="user_id" value="<?= htmlspecialchars($customer['id']); ?>">
    <div class="modal fade" id="user-bearbeiten">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Kunden bearbeiten</h4>
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
                                <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($customer['name']); ?>" required>
                            </div>

                            <!-- Nummer -->
                            <div class="form-group">
                                <strong><i class="fas fa-phone mr-1"></i> Nummer</strong>
                                <input type="text" class="form-control" name="nummer" value="<?php echo htmlspecialchars($customer['nummer']); ?>" required>
                            </div>

                            <!-- UMail -->
                            <div class="form-group">
                                <strong><i class="fas fa-envelope mr-1"></i> UMail</strong>
                                <input type="email" class="form-control" name="umail" value="<?php echo htmlspecialchars($customer['umail']); ?>" required>
                            </div>

                            <!-- Kontonummer -->
                            <div class="form-group">
                                <strong><i class="fas fa-credit-card mr-1"></i> Kontonummer</strong>
                                <input type="text" class="form-control" name="kontonummer" value="<?php echo htmlspecialchars($customer['kontonummer']); ?>" required>
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
                                <strong><i class="fas fa-user-times mr-1"></i> Gelöscht</strong> 
                                <div class="form-check">
                                    <input type="checkbox" id="gekuendigtCheckbox" class="form-check-input" name="gekuendigt" <?php echo $customer['gekuendigt'] === 'gekuendigt' ? 'checked' : ''; ?>>
                                    <label for="gekuendigtCheckbox" class="form-check-label">Kunde als Gelöscht markieren</label>
                                </div>
                            </div>
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
<!-- /modal -->

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