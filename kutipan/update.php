<?php
include('dbconn.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $year = $_POST['year'];

    // Update Kutipan Bulanan
    foreach ($_POST['bulanan'] as $month => $amount) {
        $stmt = $dbconn->prepare("UPDATE kutipan_bulanan SET amount = ? WHERE years = ? AND months = ?");
        $stmt->bind_param("dii", $amount, $year, $month);
        $stmt->execute();
    }

    // Update Jenis Kutipan
    foreach ($_POST['jenis'] as $jenis_id => $amount) {
        $stmt = $dbconn->prepare("UPDATE kutipan_jenis SET amount = ? WHERE years = ? AND category_id = ?");
        $stmt->bind_param("dii", $amount, $year, $jenis_id);
        $stmt->execute();
    }

    // Update Kutipan Sumber
    foreach ($_POST['sumber'] as $sumber_id => $amount) {
        $stmt = $dbconn->prepare("UPDATE kutipan_sumber SET amount = ? WHERE years = ? AND category_id = ?");
        $stmt->bind_param("dii", $amount, $year, $sumber_id);
        $stmt->execute();
    }

    header("Location: kutipan.php?success=1");
}
?>
