<?php
include('dbconn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $year = $_POST['year'];
    $amount = $_POST['amount'];

    $stmt = $dbconn->prepare("INSERT INTO kutipan_bulanan (years, amount) VALUES (?, ?)");
    $stmt->bind_param("ii", $year, $amount);

    if ($stmt->execute()) {
        header("Location: kutipan.php?success=1");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>
