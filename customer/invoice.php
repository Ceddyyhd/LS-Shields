<?php
include 'include/db.php';  // Datenbankverbindung einbinden

// Rechnungsnummer aus der URL holen
$invoice_number = $_GET['id'] ?? null;  // z.B. ?invoice_number=13745

// Überprüfen, ob eine gültige Rechnungsnummer übergeben wurde
if (!$invoice_number) {
    die("Fehler: Keine Rechnungsnummer übergeben.");
}

// Abfrage für die Rechnung anhand der Rechnungsnummer
$sql_invoice = "SELECT * FROM invoices WHERE invoice_number = :invoice_number";
$stmt_invoice = $conn->prepare($sql_invoice);
$stmt_invoice->execute(['invoice_number' => $invoice_number]);
$invoice = $stmt_invoice->fetch(PDO::FETCH_ASSOC);

// Überprüfen, ob eine Rechnung gefunden wurde
if (!$invoice) {
    die("Fehler: Rechnung mit dieser Rechnungsnummer nicht gefunden.");
}

// Rechnungspositionen dekodieren (falls die Positionen als JSON gespeichert sind)
$invoice_items = json_decode($invoice['description'], true);

// Überprüfen, ob die Rechnungspositionen erfolgreich dekodiert wurden
if (!$invoice_items) {
    die("Fehler beim Dekodieren der Rechnungspositionen.");
}

// Status der Rechnung auswerten
$status_class = '';
if ($invoice['status'] == 'Offen') {
    $status_class = 'badge-warning';  // Offene Rechnung
} elseif ($invoice['status'] == 'Überfällig') {
    $status_class = 'badge-danger';  // Überfällige Rechnung
} elseif ($invoice['status'] == 'Bezahlt') {
    $status_class = 'badge-success';  // Bezahlt
}

// Holen der customer_id aus der Rechnung
$customer_id = $invoice['customer_id'];

// Kundenabfrage
$sql_customer = "SELECT * FROM kunden WHERE id = :customer_id";
$stmt_customer = $conn->prepare($sql_customer);
$stmt_customer->execute(['customer_id' => $customer_id]);
$customer = $stmt_customer->fetch(PDO::FETCH_ASSOC);

// Überprüfen, ob ein Ergebnis für den Kunden gefunden wurde
if (!$customer) {
    die("Kunde nicht gefunden.");
}

// SQL-Abfrage, um alle Einstellungen zu holen
$sql = "SELECT * FROM settings";
$stmt = $conn->prepare($sql);
$stmt->execute();
$settings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// In ein assoziatives Array umwandeln, um später einfach darauf zuzugreifen
$settings_array = [];
foreach ($settings as $setting) {
    $settings_array[$setting['key_name']] = $setting['value'];
}
?>


<!DOCTYPE html>
<html lang="en">
<?php include 'include/header.php'; ?>

<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  <?php include 'include/navbar.php'; ?>

  <!-- Main Sidebar Container -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Invoice #<?= htmlspecialchars($invoice['invoice_number']); ?></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Invoice</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <!-- Main content -->
            <div class="invoice p-3 mb-3">
              <!-- title row -->
              <div class="row">
                <div class="col-12">
                  <h4>
                    <i class="fas fa-globe"></i> <?= htmlspecialchars($customer['name']); ?>
                    <small class="float-right">Date: <?= htmlspecialchars($invoice['created_at']); ?></small>
                    <?php
                    // Dynamisch Status-Badge basierend auf dem Status der Rechnung
                    $status_class = '';
                    if ($invoice['status'] == 'Offen') {
                        $status_class = 'badge-warning';  // Offene Rechnung
                    } elseif ($invoice['status'] == 'Überfällig') {
                        $status_class = 'badge-danger';  // Überfällige Rechnung
                    } elseif ($invoice['status'] == 'Bezahlt') {
                        $status_class = 'badge-success';  // Bezahlt
                    }
                    ?>
                    <span class="badge <?= $status_class; ?>"><?= htmlspecialchars($invoice['status']); ?></span>
                  </h4>
                </div>
              </div>

              <!-- info row -->
              <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
                  From
                  <address>
                    <strong><?= htmlspecialchars($settings_array['unternehmen_namen']) ?></strong><br>
                    <?= htmlspecialchars($settings_array['adresse']) ?><br>
                    <?= htmlspecialchars($settings_array['stadt_adresse']) ?><br>
                    Phone: <?= htmlspecialchars($settings_array['telefonnummer']) ?><br>
                  </address>
                </div>
                <div class="col-sm-4 invoice-col">
                  To
                  <address>
                    <strong><?= htmlspecialchars($customer['name']); ?></strong><br>
                    <?= htmlspecialchars($customer['umail']); ?><br>
                    Phone: <?= htmlspecialchars($customer['nummer']); ?><br>
                    Email: <?= htmlspecialchars($customer['umail']); ?>
                  </address>
                </div>
                <div class="col-sm-4 invoice-col">
                  <b>Invoice #<?= htmlspecialchars($invoice['invoice_number']); ?></b><br>
                  <b>Payment Due:</b> <?= htmlspecialchars($invoice['due_date']); ?><br>
                </div>
              </div>

              <!-- Table row -->
              <div class="row">
                <div class="col-12 table-responsive">
                  <table class="table table-striped">
                    <thead>
                    <tr>
                      <th>Beschreibung</th>
                      <th>Stück Preis</th>
                      <th>Anzahl</th>
                      <th>Subtotal</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($invoice_items as $item): 
                        $subtotal = $item['unit_price'] * $item['quantity'];
                    ?>
                    <tr>
                      <td><?= htmlspecialchars($item['description']); ?></td>
                      <td><?= htmlspecialchars($item['unit_price']); ?>$</td>
                      <td><?= htmlspecialchars($item['quantity']); ?></td>
                      <td><?= $subtotal; ?>$</td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                      <td>Rabatt</td>
                      <td></td>
                      <td></td>
                      <td>- <?= htmlspecialchars($invoice['discount']); ?>$</td>
                    </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              
              <div class="row">
                <!-- accepted payments column -->
                <div class="col-6">
                  <p class="lead">Bezahl Möglichkeiten:</p>
                  <img src="dist/img/credit/visa.png" alt="Visa">
                  <img src="dist/img/credit/mastercard.png" alt="Mastercard">
                  <img src="dist/img/credit/american-express.png" alt="American Express">
                  <img src="dist/img/credit/paypal2.png" alt="Paypal">

                  <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                    <strong>Überweisung: </strong> überweisen sie den Betrag an LS555555. Bitte nutzen sie als Verwendungszweck die Rechnungs Nummer
                  </p>
                </div>
                <!-- /.col -->
                <div class="col-6">
                  <p class="lead">Amount Due 2/22/2014</p>

                  <div class="table-responsive">
                    <table class="table">
                      <tr>
                        <th style="width:50%">Subtotal:</th>
                        <td>
                        <?= htmlspecialchars($invoice['price'] + $invoice['discount']); ?></td>
                      </tr>
                      <tr>
                        <th>Rabatt</th>
                        <td><?= htmlspecialchars($invoice['discount']); ?></td>
                      </tr>
                      <tr>
                        <th>Total:</th>
                        <td><?= htmlspecialchars($invoice['price']); ?></td>
                      </tr>
                    </table>
                  </div>
                </div>
                <!-- /.col -->
              </div>

              <!-- Print and PDF download -->
              <div class="row no-print">
                <div class="col-12">
                <button type="button" class="btn btn-primary float-right" style="margin-right: 5px;" onclick="window.print();">
                  <i class="fas fa-download"></i> Generate PDF
                </button>
                </div>
              </div>
            </div>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
  </div>

  <!-- Footer -->
  <footer class="main-footer no-print">
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
