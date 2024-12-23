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
              <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Activity</a></li>
              <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Timeline</a></li>
              <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Settings</a></li>
            </ul>
          </div>
          <div class="card-body">
            <div class="tab-content">
              <div class="active tab-pane" id="activity">
                <form class="form-horizontal">
                  <div class="form-group row">
                    <label for="inputName" class="col-sm-2 col-form-label">Waffenschein</label>
                    <div class="form-group d-flex align-items-center" style="flex-wrap: nowrap;">
                      <div style="margin-right: 20px; width: 200px;">
                        <label for="waffenscheinSelect">Waffenschein</label>
                        <select id="waffenscheinSelect" class="form-control" style="height: 38px; width: 100%;">
                          <option>Keiner Vorhanden</option>
                          <option>Kleiner Waffenschein</option>
                          <option>Großer & Kleiner Waffenschein</option>
                        </select>
                      </div>

                      <div style="flex-grow: 1;">
                        <label for="exampleInputFile">Datei hochladen</label>
                        <div class="input-group">
                          <div class="custom-file">
                            <input type="file" class="custom-file-input" id="exampleInputFile">
                            <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                          </div>
                          <div class="input-group-append">
                            <span class="input-group-text">Upload</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label for="inputName" class="col-sm-2 col-form-label">Führerscheine</label>
                    <div class="form-group d-flex align-items-center" style="flex-wrap: nowrap;">
                      <div style="margin-right: 20px; width: 200px;">
                        <label for="fuehrerscheinSelect">Führerscheine</label>
                        <select id="fuehrerscheinSelect" multiple class="form-control" style="height: 100px; width: 100%;">
                          <option>C</option>
                          <option>A</option>
                          <option>M2</option>
                          <option>PTL</option>
                        </select>
                      </div>

                      <div style="flex-grow: 1;">
                        <label for="exampleInputFile2">Datei hochladen</label>
                        <div class="input-group">
                          <div class="custom-file">
                            <input type="file" class="custom-file-input" id="exampleInputFile2">
                            <label class="custom-file-label" for="exampleInputFile2">Choose file</label>
                          </div>
                          <div class="input-group-append">
                            <span class="input-group-text">Upload</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label for="inputName2" class="col-sm-2 col-form-label">Arbeitsvertrag</label>
                    <div class="col-sm-10">
                      <div class="input-group">
                        <div class="custom-file">
                          <input type="file" class="custom-file-input" id="exampleInputFile">
                          <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                        </div>
                        <div class="input-group-append">
                          <span class="input-group-text">Upload</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label for="inputExperience" class="col-sm-2 col-form-label">Führungszeugnis</label>
                    <div class="col-sm-10">
                      <div class="input-group">
                        <div class="custom-file">
                          <input type="file" class="custom-file-input" id="exampleInputFile">
                          <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                        </div>
                        <div class="input-group-append">
                          <span class="input-group-text">Upload</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label for="inputSkills" class="col-sm-2 col-form-label">Erstehilfeschein</label>
                    <div class="col-sm-10">
                      <div class="input-group">
                        <div class="custom-file">
                          <input type="file" class="custom-file-input" id="exampleInputFile">
                          <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                        </div>
                        <div class="input-group-append">
                          <span class="input-group-text">Upload</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label for="inputSkills" class="col-sm-2 col-form-label">Zweitjob</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="inputSkills" placeholder="Skills">
                    </div>
                  </div>
                </form>
              </div>

              <div class="tab-pane" id="timeline">
                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#modal-default">Launch Default Modal</button>
                <div class="timeline timeline-inverse">
                  <div class="time-label">
                    <span class="bg-danger">10 Feb. 2014</span>
                  </div>

                  <div>
                    <i class="fas fa-envelope bg-primary"></i>
                    <div class="timeline-item">
                      <span class="time"><i class="far fa-clock"></i> 12:05</span>
                      <h3 class="timeline-header"><a>Cedric Schmidt</a> verwarnte ...</h3>
                      <div class="timeline-body">
                        Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles, weebly ning heekya handango imeem plugg dopplr jibjab, movity jajah plickers sifteo edmodo ifttt zimbra. Babblely odeo kaboodle quora plaxo ideeli hulu weebly balihoo...
                      </div>
                    </div>
                  </div>

                  <div>
                    <i class="fas fa-user bg-info"></i>
                    <div class="timeline-item">
                      <span class="time"><i class="far fa-clock"></i> 16:45</span>
                      <h3 class="timeline-header border-0"><a>Sarah Young</a> wurde von <a>Cedric Schmidt</a> zu Senior Officer befördert</h3>
                    </div>
                  </div>

                  <div>
                    <i class="fas fa-comments bg-warning"></i>
                    <div class="timeline-item">
                      <span class="time"><i class="far fa-clock"></i> 12:45</span>
                      <h3 class="timeline-header"><a>Jack Hunter</a> fügte eine Notiz hinzu</h3>
                      <div class="timeline-body">
                        Take me to your leader! Switzerland is small and neutral! We are more like Germany, ambitious and misunderstood!
                      </div>
                    </div>
                  </div>

                  <div class="time-label">
                    <span class="bg-success">3 Jan. 2014</span>
                  </div>

                  <div>
                    <i class="far fa-clock bg-gray"></i>
                  </div>
                </div>
              </div>

              <div class="modal fade" id="modal-default">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h4 class="modal-title">Default Modal</h4>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>

                    <div class="modal-body">
                      <div class="form-group">
                        <label>Select</label>
                        <select class="form-control">
                          <option>Notiz</option>
                          <option>Verwarnung</option>
                          <option>Kündigung</option>
                        </select>
                      </div>

                      <div class="form-group">
                        <label>Textarea</label>
                        <textarea class="form-control" rows="3" placeholder="Enter ..."></textarea>
                      </div>
                    </div>

                    <div class="modal-footer justify-content-between">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                      <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="tab-pane" id="settings">
                <form class="form-horizontal">
                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Bewertungen</label>
                    <div class="col-sm-10">
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

                      <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="ausbilderschein">
                        <label class="form-check-label" for="ausbilderschein">Ausbilderschein</label>
                      </div>

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
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Bewertungen</label>
                    <div class="col-sm-10">
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

                      <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="ausbilderschein">
                        <label class="form-check-label" for="ausbilderschein">Ausbilderschein</label>
                      </div>

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
