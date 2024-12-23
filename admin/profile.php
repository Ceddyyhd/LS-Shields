<!DOCTYPE html>
<html lang="en">
<?php include 'include/header.php'; ?>

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
              <img class="profile-user-img img-fluid img-circle" src="dist/img/user4-128x128.jpg" alt="User profile picture">
            </div>

            <h3 class="profile-username text-center">Vor Nachname</h3>
            <p class="text-muted text-center">Rang</p>

            <ul class="list-group list-group-unbordered mb-3">
              <li class="list-group-item">
                <b>Tel. Nr.:</b> <a class="float-right">555 - 667 7541</a>
              </li>
              <li class="list-group-item">
                <b>Einstellungsdatum:</b> <a class="float-right">15.12.2024</a>
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
            <strong><i class="fas fa-envelope mr-1"></i> Gmail</strong>
            <p class="text-muted">Ceddyyhd@gmail.com</p>

            <hr>
            <strong><i class="fas fa-envelope mr-1"></i> UMail</strong>
            <p class="text-muted">fal.hunter@umail.com</p>

            <hr>
            <strong><i class="fas fa-credit-card mr-1"></i> Kontonummer</strong>
            <p class="text-muted">LS84643386</p>

            <hr>
            <strong><i class="far fa-file-alt mr-1"></i> Letzte Beförderung durch</strong>
            <p class="text-muted">Kane</p>
          </div>
        </div>
      </div>

      <div class="col-md-9">
        <div class="card">
          <div class="card-header p-2">
            <ul class="nav nav-pills">
              <li class="nav-item"><a class="nav-link active" href="#settings" data-toggle="tab">Bewertungen</a></li>
            </ul>
          </div>
          <div class="card-body">
            <div class="tab-content">
              <div class="active tab-pane" id="settings">
                <form class="form-horizontal">
                  <!-- Loop for Entries -->
                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Einträge</label>
                    <div class="col-sm-10">
                      <!-- Leitstelle -->
                      <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="leitstelle">
                        <label class="form-check-label" for="leitstelle">Leitstelle</label>
                        <div class="stars ml-3" data-rating="3">
                          <i class="fas fa-star" data-value="1"></i>
                          <i class="fas fa-star" data-value="2"></i>
                          <i class="fas fa-star" data-value="3"></i>
                          <i class="far fa-star" data-value="4"></i>
                          <i class="far fa-star" data-value="5"></i>
                        </div>
                      </div>

                      <!-- Ortskenntnisse -->
                      <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="ortskentnisse">
                        <label class="form-check-label" for="ortskentnisse">Ortskenntnisse</label>
                        <div class="stars ml-3" data-rating="4">
                          <i class="fas fa-star" data-value="1"></i>
                          <i class="fas fa-star" data-value="2"></i>
                          <i class="fas fa-star" data-value="3"></i>
                          <i class="fas fa-star" data-value="4"></i>
                          <i class="far fa-star" data-value="5"></i>
                        </div>
                      </div>

                      <!-- Eventlead -->
                      <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="eventlead">
                        <label class="form-check-label" for="eventlead">Eventlead</label>
                        <div class="stars ml-3" data-rating="5">
                          <i class="fas fa-star" data-value="1"></i>
                          <i class="fas fa-star" data-value="2"></i>
                          <i class="fas fa-star" data-value="3"></i>
                          <i class="fas fa-star" data-value="4"></i>
                          <i class="fas fa-star" data-value="5"></i>
                        </div>
                      </div>

                      <!-- Ausbilderschein -->
                      <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="ausbilderschein">
                        <label class="form-check-label" for="ausbilderschein">Ausbilderschein</label>
                      </div>

                      <!-- Wiederhole für andere Einträge -->
                      <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="fasi_baller">
                        <label class="form-check-label" for="fasi_baller">Fasi Baller</label>
                        <div class="stars ml-3" data-rating="2">
                          <i class="fas fa-star" data-value="1"></i>
                          <i class="fas fa-star" data-value="2"></i>
                          <i class="far fa-star" data-value="3"></i>
                          <i class="far fa-star" data-value="4"></i>
                          <i class="far fa-star" data-value="5"></i>
                        </div>
                      </div>

                      <!-- Weitere Einträge -->
                      <!-- EH-Schulung -->
                      <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="eh_schulung">
                        <label class="form-check-label" for="eh_schulung">EH-Schulung</label>
                        <div class="stars ml-3" data-rating="3">
                          <i class="fas fa-star" data-value="1"></i>
                          <i class="fas fa-star" data-value="2"></i>
                          <i class="fas fa-star" data-value="3"></i>
                          <i class="far fa-star" data-value="4"></i>
                          <i class="far fa-star" data-value="5"></i>
                        </div>
                      </div>

                      <!-- Füge hier weitere Einträge hinzu -->
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<style>
  .stars {
    display: inline-flex;
    align-items: center;
    color: gold;
    cursor: pointer;
  }
  .stars i {
    margin-right: 2px;
    font-size: 1.2rem;
  }
</style>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 3.2.0
    </div>
    <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
  </footer>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
        const starsContainers = document.querySelectorAll('.stars');

        starsContainers.forEach(starsContainer => {
            const stars = starsContainer.querySelectorAll('i');

            stars.forEach(star => {
                star.addEventListener('click', function () {
                    const value = this.getAttribute('data-value');
                    starsContainer.setAttribute('data-rating', value);

                    // Aktualisiere die Sterneanzeige
                    stars.forEach(s => {
                        if (s.getAttribute('data-value') <= value) {
                            s.classList.remove('far'); // leere Sterne
                            s.classList.add('fas'); // gefüllte Sterne
                        } else {
                            s.classList.remove('fas'); // gefüllte Sterne
                            s.classList.add('far'); // leere Sterne
                        }
                    });

                    console.log(`Neue Bewertung für ${starsContainer.parentElement.querySelector('label').textContent}: ${value}`);
                });
            });
        });
    });
</script>
  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
</body>
</html>
