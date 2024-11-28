<?php
include('dbconn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update kutipan_bulanan
    foreach ($_POST['bulanan'] as $type => $amount) {
        $stmt = $dbconn->prepare("UPDATE kutipan_bulanan SET amount = ? WHERE type = ?");
        $stmt->bind_param("ds", $amount, $type);
        $stmt->execute();
        $stmt->close();
    }

    // Update kutipan_jenis
    foreach ($_POST['jenis'] as $type => $amount) {
        $stmt = $dbconn->prepare("UPDATE kutipan_jenis SET amount = ? WHERE type = ?");
        $stmt->bind_param("ds", $amount, $type);
        $stmt->execute();
        $stmt->close();
    }

    // Update kutipan_sumber
    foreach ($_POST['sumber'] as $type => $amount) {
        $stmt = $dbconn->prepare("UPDATE kutipan_sumber SET amount = ? WHERE type = ?");
        $stmt->bind_param("ds", $amount, $type);
        $stmt->execute();
        $stmt->close();
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
