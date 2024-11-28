<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/icon.png"/>
    <title>Kutipan</title>
    <link rel="stylesheet" href="css/mainview.css">
</head>
<?php
include('dbconn.php');

$year = $_GET['year'];

// Fetch data from all tables
$bulananQuery = $dbconn->prepare("SELECT months, amount FROM kutipan_bulanan WHERE years = ?");
$bulananQuery->bind_param("s", $year);
$bulananQuery->execute();
$bulananResult = $bulananQuery->get_result();

$jenisQuery = $dbconn->prepare("SELECT category_id, amount FROM kutipan_jenis WHERE years = ?");
$jenisQuery->bind_param("s", $year);
$jenisQuery->execute();
$jenisResult = $jenisQuery->get_result();

$sumberQuery = $dbconn->prepare("SELECT category_id, amount FROM kutipan_sumber WHERE years = ?");
$sumberQuery->bind_param("s", $year);
$sumberQuery->execute();
$sumberResult = $sumberQuery->get_result();

// Generate the form dynamically
echo "<h3>Kutipan Bulanan</h3>";
while ($row = $bulananResult->fetch_assoc()) {
    echo "
        <div>
            <label>{$row['months']}</label>
            <input type='text' name='bulanan[{$row['months']}]' value='{$row['amount']}'>
        </div>
    ";
}

echo "<h3>Kutipan Jenis</h3>";
while ($row = $jenisResult->fetch_assoc()) {
    echo "
        <div>
            <label>{$row['category_id']}</label>
            <input type='text' name='jenis[{$row['category_id']}]' value='{$row['amount']}'>
        </div>
    ";
}

echo "<h3>Kutipan Sumber</h3>";
while ($row = $sumberResult->fetch_assoc()) {
    echo "
        <div>
            <label>{$row['category_id']}</label>
            <input type='text' name='sumber[{$row['category_id']}]' value='{$row['amount']}'>
        </div>
    ";
}

$bulananQuery->close();
$jenisQuery->close();
$sumberQuery->close();
?>
</html>