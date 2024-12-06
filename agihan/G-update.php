<?php
include('../dbconn.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $year = $_POST['year'];

    foreach ($_POST['agihan'] as $jenis_id => $amount) {
        $stmt = $dbconn->prepare("UPDATE agihan_category SET amount = ? WHERE years = ? AND category_id = ?");
        $stmt->bind_param("dii", $amount, $year, $jenis_id);
        $stmt->execute();
    }

    foreach ($_POST['asnaf'] as $sumber_id => $amount) {
        $stmt = $dbconn->prepare("UPDATE agihan_asnaf SET amount = ? WHERE years = ? AND category_id = ?");
        $stmt->bind_param("dii", $amount, $year, $sumber_id);
        $stmt->execute();
    }

    header("Location: ../agihan/A-agihan.php?success=1");
}
?>
