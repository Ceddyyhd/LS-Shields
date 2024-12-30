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

<?php
include 'db.php';  // Datenbankverbindung einbinden

// Kunden-ID aus der URL holen
$customer_id = $_GET['id'];  // z.B. ?id=1

// Überprüfen, ob eine gültige Kunden-ID übergeben wurde
if (!isset($customer_id) || !is_numeric($customer_id)) {
    die("Ungültige Kunden-ID.");
}

// Abfrage für die Rechnung des Kunden
$sql_invoice = "SELECT * FROM invoices WHERE customer_id = :customer_id ORDER BY created_at DESC";
$stmt_invoice = $conn->prepare($sql_invoice);
$stmt_invoice->execute(['customer_id' => $customer_id]);
$invoice = $stmt_invoice->fetch(PDO::FETCH_ASSOC);

// Überprüfen, ob ein Ergebnis für die Rechnung gefunden wurde
if (!$invoice) {
    die("Rechnung nicht gefunden.");
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

// Kundenabfrage
$sql_customer = "SELECT * FROM kunden WHERE id = :customer_id";
$stmt_customer = $conn->prepare($sql_customer);
$stmt_customer->execute(['customer_id' => $customer_id]);
$customer = $stmt_customer->fetch(PDO::FETCH_ASSOC);

// Überprüfen, ob ein Ergebnis für den Kunden gefunden wurde
if (!$customer) {
    die("Kunde nicht gefunden.");
}

?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Invoice</h1>
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
                    <i class="fas fa-globe"></i> LS Shields
                    <small class="float-right"><?= htmlspecialchars($invoice['created_at']) ?></small>
                    <span class="badge <?= $status_class; ?>"><?= htmlspecialchars($invoice['status']); ?></span>
                  </h4>
                </div>
                <!-- /.col -->
              </div>
              <!-- info row -->
              <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
                  From
                  <address>
                    <strong>LS-Shields</strong><br>
                    Adresse<br>
                    Los Santos<br>
                    Phone: XXX<br>
                    Email: XXX@XXX.XXX
                  </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                  To
                  <address>
                    <strong><?= htmlspecialchars($customer['name']); ?></strong><br>
                    <?= htmlspecialchars($customer['umail']); ?><br>
                    Phone: <?= htmlspecialchars($customer['nummer']); ?><br>
                    Email: <?= htmlspecialchars($customer['umail']); ?>
                  </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                  <b>Invoice #<?= htmlspecialchars($invoice['invoice_number']); ?></b><br>
                  <br>
                  <b>Payment Due:</b> <?= htmlspecialchars($invoice['due_date']); ?><br>
                  <b>Account:</b> <?= htmlspecialchars($customer['kontonummer']); ?>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->

              <!-- Table row -->
              <div class="row">
                <div class="col-12 table-responsive">
                  <table class="table table-striped">
                    <thead>
                    <tr>
                      <th>Description</th>
                      <th>Unit Price</th>
                      <th>Quantity</th>
                      <th>Subtotal</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($invoice_items as $item): ?>
                    <tr>
                      <td><?= htmlspecialchars($item['description']); ?></td>
                      <td><?= htmlspecialchars($item['unit_price']); ?>$</td>
                      <td><?= htmlspecialchars($item['quantity']); ?></td>
                      <td><?= htmlspecialchars($item['unit_price'] * $item['quantity']); ?>$</td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->

              <div class="row" style="margin-left: 90%;">
                <div class="col-6">
                  <p class="lead">Amount Due</p>

                  <div class="table-responsive">
                    <table class="table">
                      <tr>
                        <th>Total:</th>
                        <td>
                          <?php
                          $total = 0;
                          foreach ($invoice_items as $item) {
                              $total += $item['unit_price'] * $item['quantity'];
                          }
                          echo $total . '$';
                          ?>
                        </td>
                      </tr>
                    </table>
                  </div>
                </div>
              </div>

              <div class="row no-print">
                <div class="col-12">
                  <button type="button" class="btn btn-primary float-right" style="margin-right: 5px;">
                    <i class="fas fa-download"></i> Generate PDF
                  </button>
                </div>
              </div>
            </div>
            <!-- /.invoice -->
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <footer class="main-footer no-print">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 3.2.0
    </div>
    <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
  </footer>

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
