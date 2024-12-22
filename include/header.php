<header class="header_section">
  <div class="header_top">
    <div class="container-fluid">
      <div class="contact_link-container">
        <a href="" class="contact_link1">
          <i class="fa fa-map-marker" aria-hidden="true"></i>
          <span>Lorem ipsum dolor sit amet,</span>
        </a>
        <a href="" class="contact_link2">
          <i class="fa fa-phone" aria-hidden="true"></i>
          <span>Call : 6004</span>
        </a>
      </div>
    </div>
  </div>
  <div class="header_bottom">
    <div class="container-fluid">
      <nav class="navbar navbar-expand-lg custom_nav-container">
        <a class="navbar-brand" href="index.php">
          <span>LS-Shields</span>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class=""></span>
        </button>
        <div class="collapse navbar-collapse ml-auto" id="navbarSupportedContent">
          <ul class="navbar-nav">
            <li class="nav-item <?= basename($_SERVER['SCRIPT_NAME']) == 'index.php' ? 'active' : '' ?>">
              <a class="nav-link" href="index.php">Home</a>
            </li>
            <li class="nav-item <?= basename($_SERVER['SCRIPT_NAME']) == 'about.php' ? 'active' : '' ?>">
              <a class="nav-link" href="about.php">Über uns</a>
            </li>
            <li class="nav-item <?= basename($_SERVER['SCRIPT_NAME']) == 'service.php' ? 'active' : '' ?>">
              <a class="nav-link" href="service.php">Services</a>
            </li>
            <li class="nav-item <?= basename($_SERVER['SCRIPT_NAME']) == 'guard.php' ? 'active' : '' ?>">
              <a class="nav-link" href="guard.php">Unser Team</a>
            </li>
            <li class="nav-item <?= basename($_SERVER['SCRIPT_NAME']) == 'partner.php' ? 'active' : '' ?>">
              <a class="nav-link" href="partner.php">Partner</a>
            </li>
            <li class="nav-item <?= basename($_SERVER['SCRIPT_NAME']) == 'galerie.php' ? 'active' : '' ?>">
              <a class="nav-link" href="galerie.php">Galerie</a>
            </li>
            <li class="nav-item <?= basename($_SERVER['SCRIPT_NAME']) == 'contact.php' ? 'active' : '' ?>">
              <a class="nav-link" href="contact.php">Contact us</a>
            </li>
            <li class="nav-item <?= basename($_SERVER['SCRIPT_NAME']) == 'impressum.php' ? 'active' : '' ?>">
              <a class="nav-link" href="impressum.php">Impressum</a>
            </li>
          </ul>
        </div>
      </nav>
    </div>
  </div>
</header>
