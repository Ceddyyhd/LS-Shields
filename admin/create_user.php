<?php
// session_start(); // Später Rechteprüfung einfügen
include 'include/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Benutzer erstellen</title>
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<body>
<div class="container mt-5">
  <div class="card card-outline card-primary">
    <div class="card-header">
      <h3 class="card-title">Benutzer erstellen</h3>
    </div>
    <div class="card-body">
      <form id="createUserForm">
        <div class="form-group">
          <label for="email">E-Mail-Adresse</label>
          <input type="email" id="email" class="form-control" placeholder="E-Mail-Adresse" required>
        </div>
        <div class="form-group">
          <label for="password">Passwort</label>
          <input type="password" id="password" class="form-control" placeholder="Passwort" required>
        </div>
        <button type="submit" class="btn btn-primary">Benutzer erstellen</button>
      </form>
      <div id="createUserMessage" style="margin-top: 20px;"></div>
    </div>
  </div>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script>
$(document).ready(function () {
  $('#createUserForm').submit(function (e) {
    e.preventDefault();
    const email = $('#email').val();
    const password = $('#password').val();

    $.ajax({
      url: 'include/ajax_create_user.php',
      type: 'POST',
      data: { email, password },
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          $('#createUserMessage').html(`<div class="alert alert-success">${response.message}</div>`);
          $('#createUserForm')[0].reset();
        } else {
          $('#createUserMessage').html(`<div class="alert alert-danger">${response.message}</div>`);
        }
      },
      error: function () {
        $('#createUserMessage').html('<div class="alert alert-danger">Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.</div>');
      }
    });
  });
});
</script>
</body>
</html>
