<?php
include('../dbconn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $year = $_POST['year'];
    $category_id = $_POST['category_id'];
    $amount = $_POST['amount'];

    // Check if record exists
    $stmt = $dbconn->prepare("SELECT amount FROM amil_expense WHERE years = ? AND category_id = ?");
    $stmt->bind_param("ii", $year, $category_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Record exists, update the amount
        $row = $result->fetch_assoc();
        $new_amount = $row['amount'] + $amount;

        $update_stmt = $dbconn->prepare("UPDATE amil_expense SET amount = ? WHERE years = ? AND category_id = ?");
        $update_stmt->bind_param("dii", $new_amount, $year, $category_id);
        if ($update_stmt->execute()) {
            header("Location: ../amil/A-amil.php?status=success&type=perbelanjaan");
        } else {
            header("Location: ../amil/A-amil.php?status=error&type=perbelanjaan");
        }
    } else {
        // Record doesn't exist, insert new record
        $insert_stmt = $dbconn->prepare("INSERT INTO amil_expense (years, category_id, amount) VALUES (?, ?, ?)");
        $insert_stmt->bind_param("iid", $year, $category_id, $amount);
        if ($insert_stmt->execute()) {
            header("Location: ../amil/A-amil.php?status=success&type=perbelanjaan");
        } else {
            header("Location: ../amil/A-amil.php?status=error&type=perbelanjaan");
        }
    }
}
?>
