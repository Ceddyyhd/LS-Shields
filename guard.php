<!DOCTYPE html>
<html>

<head>
  <!-- Basic -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- Mobile Metas -->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <!-- Site Metas -->
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">

  <title>Guarder</title>

  <!-- bootstrap core css -->
  <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />

  <!-- fonts style -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700|Poppins:400,600,700&display=swap" rel="stylesheet" />

  <!-- Custom styles for this template -->
  <link href="css/style.css" rel="stylesheet" />
  <!-- responsive style -->
  <link href="css/responsive.css" rel="stylesheet" />
</head>

<body class="sub_page">
  <div class="hero_area">
    <!-- header section strats -->
    <div class="hero_bg_box">
      <div class="img-box">
        <img src="images/hero-bg.jpg" alt="">
      </div>
    </div>

    <?php include 'include/header.php'; ?>
    <!-- end header section -->
  </div>

  <!-- team section -->

  <?php
// Beispiel-PHP-Code zum Abrufen der Mitarbeiterdaten (hier wurde bereits das Profilbild angepasst)
include 'db.php';

$stmtEmployees = $conn->prepare("SELECT u.id, u.name, u.profile_image, u.role_id, r.name as role_name, r.level
                                 FROM users u
                                 JOIN roles r ON u.role_id = r.id
                                 WHERE u.gekuendigt = 'no_kuendigung' AND u.bewerber = 'nein'");
$stmtEmployees->execute();
$employees = $stmtEmployees->fetchAll(PDO::FETCH_ASSOC);

$sortedEmployees = [];
foreach ($employees as $employee) {
    $sortedEmployees[$employee['level']][] = $employee;
}
?>

<section class="team_section layout_padding">
    <div class="container">
        <div class="heading_container heading_center">
            <h2>Unsere Mitarbeiter</h2>
            <p>Unsere Mitarbeiter sind erfahrene Fachkräfte, die für höchste Professionalität, Freundlichkeit und Kompetenz stehen.</p>
        </div>

        <?php
        foreach ($sortedEmployees as $level => $levelEmployees):
        ?>
            <div class="heading_container heading_center">
                <h2><?php echo htmlspecialchars($level); ?></h2>
            </div>
            <div class="row justify-content-center">
                <?php
                $numEmployees = count($levelEmployees);

                // Wenn nur 1 Mitarbeiter in der Ebene
                if ($numEmployees == 1): 
                    $employee = $levelEmployees[0];
                ?>
                    <div class="col-md-4 text-center">
                        <div class="box">
                            <div class="img-box">
                                <img src="/admin<?php echo htmlspecialchars($employee['profile_image']); ?>" alt="" class="img-fluid small-img">
                            </div>
                            <div class="detail-box">
                                <h5><?php echo htmlspecialchars($employee['name']); ?></h5>
                                <h6><?php echo htmlspecialchars($employee['role_name']); ?></h6>
                            </div>
                        </div>
                    </div>
                <?php // Wenn 2 Mitarbeiter in diesem Level
                elseif ($numEmployees == 2): 
                    // 1. Mitarbeiter - links
                    $employee1 = $levelEmployees[0];
                    // 2. Mitarbeiter - rechts
                    $employee2 = $levelEmployees[1];
                ?>
                    <div class="col-md-4 text-center">
                        <div class="box">
                            <div class="img-box">
                                <img src="/admin/<?php echo htmlspecialchars($employee1['profile_image']); ?>" alt="" class="img-fluid medium-img">
                            </div>
                            <div class="detail-box">
                                <h5><?php echo htmlspecialchars($employee1['name']); ?></h5>
                                <h6><?php echo htmlspecialchars($employee1['role_name']); ?></h6>
                            </div>
                        </div>
                    </div>
                    <!-- Leerer Platz für die Mitte -->
                    <div class="col-md-4"></div>
                    <div class="col-md-4 text-center">
                        <div class="box">
                            <div class="img-box">
                                <img src="/admin/<?php echo htmlspecialchars($employee2['profile_image']); ?>" alt="" class="img-fluid medium-img">
                            </div>
                            <div class="detail-box">
                                <h5><?php echo htmlspecialchars($employee2['name']); ?></h5>
                                <h6><?php echo htmlspecialchars($employee2['role_name']); ?></h6>
                            </div>
                        </div>
                    </div>
                <?php // Wenn 3 oder mehr Mitarbeiter in diesem Level
                elseif ($numEmployees >= 3): 
                    foreach ($levelEmployees as $employee): ?>
                        <div class="col-md-4 text-center">
                            <div class="box">
                                <div class="img-box">
                                    <img src="/admin/<?php echo htmlspecialchars($employee['profile_image']); ?>" alt="" class="img-fluid">
                                </div>
                                <div class="detail-box">
                                    <h5><?php echo htmlspecialchars($employee['name']); ?></h5>
                                    <h6><?php echo htmlspecialchars($employee['role_name']); ?></h6>
                                </div>
                            </div>
                        </div>
                    <?php endforeach;
                endif;
                ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<style>
    /* Bildgröße für 1 Mitarbeiter */
    .small-img {
        max-width: 50%; /* Kleinere Bilder für den einzelnen Mitarbeiter */
        height: auto;
        object-fit: cover;
    }

    /* Bildgröße für 2 Mitarbeiter */
    .medium-img {
        max-width: 70%; /* Mittelgroße Bilder für 2 Mitarbeiter */
        height: auto;
        object-fit: cover;
    }

    .img-box img {
        max-width: 100%;
        height: 400px;
        object-fit: cover;
    }

    .text-center {
        text-align: center;
    }

    .row.justify-content-center {
        display: flex;
        justify-content: center;
    }

    .box {
        padding: 15px;
        margin: 10px;
        background-color: #f0f0f0;
        border-radius: 10px;
        display: inline-block;
    }

    .heading_container {
        margin-bottom: 30px;
    }

    .detail-box {
        background: rgba(0, 0, 0, 0.5);
        color: #fff;
        padding: 10px;
        border-radius: 5px;
        text-align: center;
    }

    .detail-box h5, .detail-box h6 {
        margin: 5px;
    }
</style>





  <!-- end team section -->

   <!-- info section -->
   <section class="info_section ">
    <div class="container">
      <div class="row">
        <div class="col-md-3">
          <div class="info_logo">
            <a class="navbar-brand" href="index.html">
              <span>
                LS-Shields
              </span>
            </a>
            <p>
              Ihr Unternehmen im Thema Sicherheit.
            </p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="info_links">
            <h5>
              
            </h5>
            <ul>
              <li>
                <a href="">
                  
                </a>
              </li>
              <li>
                <a href="">
                  
                </a>
              </li>
              <li>
                <a href="">
                  
                </a>
              </li>
              <li>
                <a href="">
                  
                </a>
              </li>
            </ul>
          </div>
        </div>
        <div class="col-md-3">
          <div class="info_info">
            <h5>
              Kontakt Daten
            </h5>
          </div>
          <div class="info_contact">
            <a href="" class="">
              <i class="fa fa-map-marker" aria-hidden="true"></i>
              <span>
                Lorem ipsum dolor sit amet,
              </span>
            </a>
            <a href="" class="">
              <i class="fa fa-phone" aria-hidden="true"></i>
              <span>
                Call : 6004
              </span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- end info_section -->




  <!-- footer section -->
  <footer class="container-fluid footer_section">
    <p>
      Diese Seite, ist eine fiktive Seite. Jegliche Inhalte sind nicht real und gehören zum Rollenspiel Projekt "Unity-life.de". </br>
      &copy; <span id="currentYear"></span> All Rights Reserved. Design by
      <a href="https://html.design/">Free Html Templates</a> Distribution by
      <a href="https://themewagon.com">ThemeWagon</a>
    </p>
  </footer>
  <!-- footer section -->

  <script src="js/jquery-3.4.1.min.js"></script>
  <script src="js/bootstrap.js"></script>
  <script src="js/custom.js"></script>
</body>

</html>