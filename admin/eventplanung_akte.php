<!DOCTYPE html>
<html lang="en">
<?php include 'include/header.php'; ?>

<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  <?php include 'include/navbar.php'; ?>

  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
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
            </div>
          </div>

          <div class="col-md-9">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#plan" data-toggle="tab">Plan</a></li>
                  <li class="nav-item"><a class="nav-link" href="#plan-bearbeiten" data-toggle="tab">Plan Bearbeiten</a></li>
                  <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Settings</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="plan">
                    <h1><u>Heading Of Message</u></h1>
                    <h4><span style="font-size: 1rem;">Folgende Regeln:</span></h4>
                    <ul>
                      <li>List item one</li>
                      <li>List item two</li>
                      <li>List item three</li>
                      <li>List item four</li>
                    </ul>
                    <p>Standort Übersicht</p>
                    <p><img src="https://img.ceddyyhd2.eu/c51bef7c956b18e5d652994cb87d7584.png" alt="Image"></p>
                  </div>

                  <div class="tab-pane" id="plan-bearbeiten">
                    <!-- Summernote Editor Section -->
                    <section class="content">
                      <div class="container-fluid">
                        <div class="card card-outline card-info">
                          <div class="card-header">
                            <h3 class="card-title">Summernote</h3>
                          </div>
                          <div class="card-body">
                            <textarea id="summernote" name="summernote_data"></textarea>
                          </div>
                        </div>
                      </div>
                    </section>
                  </div>

                  <div class="tab-pane" id="settings">
                    <!-- Settings content here -->
                  </div>
                </div>
              </div><!-- /.card-body -->
            </div>
          </div>
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

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside><!-- /.control-sidebar -->
</div><!-- ./wrapper -->

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Summernote JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<!-- AdminLTE JS -->
<script src="dist/js/adminlte.min.js"></script>

<script>
  $(document).ready(function () {
    // Summernote Initialisierung
    $('#summernote').summernote({
      height: 200,  // Höhe des Editors
      codemirror: { // CodeMirror Optionen für den HTML-Editor
        theme: 'monokai'
      }
    });
  });
</script>

</body>
</html>
