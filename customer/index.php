<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
    <?php include 'include/header.php'; 
    $user_id = $_SESSION['user_id'] ?? 'Keine ID vorhanden';
    include 'include/db.php';
    
    // SQL-Abfrage, um die Rechnungen für den aktuellen Kunden abzurufen
$sql = "SELECT invoice_number, description, price, discount, created_at, due_date, status 
FROM invoices 
WHERE customer_id = :user_id 
ORDER BY created_at DESC"; // Optional: nach dem Erstellungsdatum sortieren
$stmt = $conn->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <?php include 'include/navbar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Starter Page</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Starter Page</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    

    
    <div class="card-body">
      <div class="callout callout-danger">
        <h5>Wichtige Ankündigung!</h5>

        <p>Diese Seite befindet sich noch in aufbau!</p>
      </div>
    </div>

    <div class="card">
  <div class="card-header border-transparent">
    <h3 class="card-title">Latest Invoices</h3>

    <div class="card-tools">
      <button type="button" class="btn btn-tool" data-card-widget="collapse">
        <i class="fas fa-minus"></i>
      </button>
      <button type="button" class="btn btn-tool" data-card-widget="remove">
        <i class="fas fa-times"></i>
      </button>
    </div>
  </div>
  <!-- /.card-header -->
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table m-0">
        <thead>
          <tr>
            <th>Invoice Number</th>
            <th>Description</th>
            <th>Status</th>
            <th>Amount</th>
          </tr>
        </thead>
        <tbody>
          <?php
            // Überprüfen, ob Rechnungen vorhanden sind
            if (!empty($invoices)) {
                foreach ($invoices as $invoice) {
                    echo "<tr>";
                    echo "<td><a href='invoice_detail.php?id=" . $invoice['invoice_number'] . "'>" . $invoice['invoice_number'] . "</a></td>";
                    echo "<td>" . htmlspecialchars($invoice['description']) . "</td>";
                    echo "<td><span class='badge badge-" . getInvoiceStatusClass($invoice['status']) . "'>" . $invoice['status'] . "</span></td>";
                    $totalAmount = $invoice['price'] - $invoice['discount'];
                    echo "<td>" . number_format($totalAmount, 2) . " €</td>"; // Gesamtbetrag nach Rabatt
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No invoices found.</td></tr>";
            }
          ?>
        </tbody>
      </table>
    </div>
    <!-- /.table-responsive -->
  </div>
  <!-- /.card-body -->
</div>


    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
    <div class="p-3">
      <h5>Title</h5>
      <p>Sidebar content</p>
    </div>
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
      Anything you want
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>
