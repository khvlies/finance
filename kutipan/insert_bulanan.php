<?php
include('../dbconn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $year = $_POST['year'];
    $month = $_POST['month'];
    $amount = $_POST['amount'];

    // Check if record exists
    $stmt = $dbconn->prepare("SELECT amount FROM kutipan_bulanan WHERE years = ? AND months = ?");
    $stmt->bind_param("ii", $year, $month);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Record exists, update the amount
        $row = $result->fetch_assoc();
        $new_amount = $row['amount'] + $amount;

        $update_stmt = $dbconn->prepare("UPDATE kutipan_bulanan SET amount = ? WHERE years = ? AND months = ?");
        $update_stmt->bind_param("dii", $new_amount, $year, $month);
        if ($update_stmt->execute()) {
            header("Location: ../kutipan/A-kutipan.php?status=success&type=bulanan");
        } else {
            header("Location: ../kutipan/A-kutipan.php?status=error&type=bulanan");
        }
    } else {
        // Record doesn't exist, insert new record
        $insert_stmt = $dbconn->prepare("INSERT INTO kutipan_bulanan (years, months, amount) VALUES (?, ?, ?)");
        $insert_stmt->bind_param("iid", $year, $month, $amount);
        if ($insert_stmt->execute()) {
            header("Location: ../kutipan/A-kutipan.php?status=success&type=bulanan");
        } else {
            header("Location: ../kutipan/A-kutipan.php?status=error&type=bulanan");
        }
    }
}
?>
