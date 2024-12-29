<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kundenverwaltung</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>

<div class="container mt-5">
    <!-- Kunden-Tabelle -->
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Unternehmen</th>
                <th>Ansprechperson</th>
                <th>Telefonnummer</th>
                <th>Status</th>
                <th>Aktionen</th>
            </tr>
        </thead>
        <tbody>
        <?php
        include('db.php');
        
        // Alle Kunden abfragen
        $stmt = $conn->prepare("SELECT * FROM Kunden");
        $stmt->execute();
        $kunden = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Kunden anzeigen
        foreach ($kunden as $kunde):
        ?>
            <tr data-id="<?= $kunde['id'] ?>">
                <td><?= htmlspecialchars($kunde['id']) ?></td>
                <td><?= htmlspecialchars($kunde['name']) ?></td>
                <td><?= htmlspecialchars($kunde['nummer']) ?></td>
                <td><?= htmlspecialchars($kunde['umail']) ?></td>
                <td><?= htmlspecialchars($kunde['geloescht']) ?></td>
                <td>
                    <button type="button" class="btn btn-warning" onclick="markAsDeleted(<?= $kunde['id'] ?>)">Als gelöscht markieren</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Modal zum Erstellen eines neuen Kunden -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-kunde-create">Kunden erstellen</button>

    <div class="modal fade" id="modal-kunde-create" tabindex="-1" role="dialog" aria-labelledby="modal-kunde-create-label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-kunde-create-label">Kunde erstellen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="createCustomerForm">
                        <div class="form-group">
                            <label for="umail">E-Mail</label>
                            <input type="email" class="form-control" id="umail" name="umail" required>
                        </div>
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="nummer">Telefonnummer</label>
                            <input type="text" class="form-control" id="nummer" name="nummer">
                        </div>
                        <div class="form-group">
                            <label for="kontonummer">Kontonummer</label>
                            <input type="text" class="form-control" id="kontonummer" name="kontonummer">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
                    <button type="button" class="btn btn-primary" id="saveCustomerBtn">Speichern</button>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    // Funktion zum Markieren eines Kunden als gelöscht
    function markAsDeleted(kundenId) {
        $.ajax({
            url: 'kunden_update.php',
            type: 'POST',
            data: {kunden_id: kundenId},
            success: function(response) {
                alert('Kunde wurde als gelöscht markiert.');
                location.reload();  // Seite neu laden
            },
            error: function() {
                alert('Fehler beim Markieren des Kunden.');
            }
        });
    }

    // Formular zum Erstellen eines neuen Kunden
    document.getElementById('saveCustomerBtn').addEventListener('click', function() {
        const formData = new FormData(document.getElementById('createCustomerForm'));

        // AJAX-Anfrage zum Erstellen des Kunden
        fetch('kunden_create.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Kunde erfolgreich erstellt!');
                $('#modal-kunde-create').modal('hide');  // Modal schließen
                location.reload();  // Seite neu laden
            } else {
                alert('Fehler: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Fehler:', error);
            alert('Ein unerwarteter Fehler ist aufgetreten.');
        });
    });
</script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js"></script>

</body>
</html>
