<?php
session_start();
include 'include/db.php';

// Mitarbeiter aus der users-Tabelle holen, wo bewerber = 'nein'
$stmt = $conn->prepare("SELECT id, username FROM users WHERE bewerber = 'nein'");
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Gehalt, Anteil, Trinkgeld und Verlauf
// Gehalt hinzufügen (POST-Request)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_finance'])) {
    $userId = $_POST['user_id'];
    $betrag = $_POST['betrag'];
    $kategorie = $_POST['kategorie'];
    $notiz = $_POST['notiz'];
    $typ = 'Ausgabe'; // Typ für Gehalt, Anteil, Trinkgeld ist immer 'Ausgabe'
    $erstelltVon = $_SESSION['username']; // Der Benutzer, der den Eintrag erstellt

    $insertStmt = $conn->prepare("
        INSERT INTO finanzen (typ, kategorie, notiz, betrag, erstellt_von)
        VALUES (:typ, :kategorie, :notiz, :betrag, :erstellt_von)
    ");
    $insertStmt->bindParam(':typ', $typ);
    $insertStmt->bindParam(':kategorie', $kategorie);
    $insertStmt->bindParam(':notiz', $notiz);
    $insertStmt->bindParam(':betrag', $betrag);
    $insertStmt->bindParam(':erstellt_von', $erstelltVon);
    $insertStmt->execute();

    echo json_encode(['success' => true, 'message' => 'Buchung erfolgreich!']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mitarbeiter Gehalt & Finanzen</title>
    <link rel="stylesheet" href="path_to_your_css_file.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
</head>
<body>
    <div class="wrapper">
        <!-- Navbar -->
        <?php include 'include/navbar.php'; ?>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Mitarbeiter Gehalt & Finanzen</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Mitarbeiter Finanzen</h3>
                </div>
                <div class="card-body">
                    <table id="employeeTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Benutzername</th>
                                <th>Gehalt/Anteil/Trinkgeld hinzufügen</th>
                                <th>Verlauf</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($employees as $employee): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($employee['username']); ?></td>
                                <td>
                                    <button class="btn btn-primary btn-sm add-finance" data-userid="<?php echo $employee['id']; ?>" data-username="<?php echo $employee['username']; ?>">
                                        Gehalt/Anteil/Trinkgeld hinzufügen
                                    </button>
                                </td>
                                <td>
                                    <button class="btn btn-info btn-sm view-finance" data-userid="<?php echo $employee['id']; ?>">
                                        Verlauf anzeigen
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal für Gehalt/Anteil/Trinkgeld hinzufügen -->
    <div class="modal fade" id="financeModal" tabindex="-1" role="dialog" aria-labelledby="financeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="financeModalLabel">Gehalt/Anteil/Trinkgeld hinzufügen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="financeForm">
                        <input type="hidden" id="financeUserId" name="user_id">
                        <div class="form-group">
                            <label for="betrag">Betrag</label>
                            <input type="number" step="0.01" class="form-control" id="betrag" name="betrag" required>
                        </div>
                        <div class="form-group">
                            <label for="kategorie">Kategorie</label>
                            <select class="form-control" id="kategorie" name="kategorie" required>
                                <option value="Gehalt">Gehalt</option>
                                <option value="Anteil">Anteil</option>
                                <option value="Trinkgeld">Trinkgeld</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="notiz">Notiz</label>
                            <textarea class="form-control" id="notiz" name="notiz" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
                    <button type="button" class="btn btn-primary" id="saveFinanceBtn">Speichern</button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery und andere Skripte -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="dist/js/adminlte.min.js"></script>

    <script>
        // Button für Gehalt/Anteil/Trinkgeld hinzufügen
        $(document).on('click', '.add-finance', function() {
            const userId = $(this).data('userid');
            const username = $(this).data('username');
            $('#financeUserId').val(userId);
            $('#financeModalLabel').text('Gehalt/Anteil/Trinkgeld für ' + username + ' hinzufügen');
            $('#financeModal').modal('show');
        });

        // Speichern der Finanzen mit Ajax
        $('#saveFinanceBtn').click(function() {
            const formData = $('#financeForm').serialize();
            $.ajax({
                url: 'rangverwaltung.php',
                method: 'POST',
                data: formData + '&add_finance=true',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        $('#financeModal').modal('hide');
                    } else {
                        alert('Fehler: ' + response.message);
                    }
                },
                error: function() {
                    alert('Es gab einen Fehler bei der Anfrage.');
                }
            });
        });

        // Verlauf anzeigen
        $(document).on('click', '.view-finance', function() {
            const userId = $(this).data('userid');
            window.location.href = 'view_finance.php?user_id=' + userId;
        });
    </script>
</body>
</html>
