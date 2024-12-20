<?php
include('../dbconn.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $year = $_POST['year'];

    foreach ($_POST['pendapatan'] as $income_id => $amount) {
        $stmt = $dbconn->prepare("UPDATE amil_income SET amount = ? WHERE years = ? AND category_id = ?");
        $stmt->bind_param("dii", $amount, $year, $income_id);
        $stmt->execute();
    }

    foreach ($_POST['perbelanjaan'] as $expense_id => $amount) {
        $stmt = $dbconn->prepare("UPDATE amil_expense SET amount = ? WHERE years = ? AND category_id = ?");
        $stmt->bind_param("dii", $amount, $year, $expense_id);
        $stmt->execute();
    }

    header("Location: ../amil/A-amil.php?success=1");
}
?>
