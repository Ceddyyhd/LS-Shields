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
include '/admin/include/db.php';

// Abfrage der Mitarbeiter, die 'gekündigt' = 'no_kuendigung' und 'bewerber' = 'nein' sind
$stmtEmployees = $conn->prepare("SELECT u.id, u.name, u.profile_image, u.role_id, r.name as role_name, r.level
                                 FROM users u
                                 JOIN roles r ON u.role_id = r.id
                                 WHERE u.gkuendigt = 'no_kuendigung' AND u.bewerber = 'nein'");
$stmtEmployees->execute();
$employees = $stmtEmployees->fetchAll(PDO::FETCH_ASSOC);

// Nach Level sortieren, damit du die Mitarbeiter je nach Level sortieren kannst
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
        // Durch die Level iterieren und die Mitarbeiter anzeigen
        foreach ($sortedEmployees as $level => $levelEmployees):
        ?>
            <div class="heading_container heading_center">
                <h2><?php echo htmlspecialchars($level); ?></h2>
            </div>
            <div class="row">
                <?php
                // Abhängig von der Anzahl der Mitarbeiter im Level die Anzeige anpassen
                $numEmployees = count($levelEmployees);

                if ($numEmployees == 1): // Nur ein Mitarbeiter, in der Mitte anzeigen
                    $employee = $levelEmployees[0];
                ?>
                    <div class="col-md-12">
                        <div class="box">
                            <div class="img-box">
                                <img src="<?php echo htmlspecialchars($employee['profile_image']); ?>" alt="">
                            </div>
                            <div class="detail-box">
                                <h5><?php echo htmlspecialchars($employee['name']); ?></h5>
                                <h6><?php echo htmlspecialchars($employee['role_name']); ?></h6>
                            </div>
                        </div>
                    </div>
                <?php elseif ($numEmployees == 2): // Zwei Mitarbeiter, links und rechts anzeigen
                    foreach ($levelEmployees as $employee): ?>
                        <div class="col-md-6">
                            <div class="box">
                                <div class="img-box">
                                    <img src="<?php echo htmlspecialchars($employee['profile_image']); ?>" alt="">
                                </div>
                                <div class="detail-box">
                                    <h5><?php echo htmlspecialchars($employee['name']); ?></h5>
                                    <h6><?php echo htmlspecialchars($employee['role_name']); ?></h6>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php elseif ($numEmployees >= 3): // Drei oder mehr Mitarbeiter, in einer Reihe anzeigen
                    foreach ($levelEmployees as $employee): ?>
                        <div class="col-md-4">
                            <div class="box">
                                <div class="img-box">
                                    <img src="<?php echo htmlspecialchars($employee['profile_image']); ?>" alt="">
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