<?php
include 'db.php';

if (isset($_GET['vehicle_id'])) {
    $vehicle_id = $_GET['vehicle_id'];
    
    $sql = "SELECT * FROM vehicles WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$vehicle_id]);
    $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($vehicle);
}
?>
