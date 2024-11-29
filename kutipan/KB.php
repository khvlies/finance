<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/icon.png"/>
    <title>Kutipan</title>
    <link rel="stylesheet" href="../css/mainview.css">
</head>
<?php
include('../dbconn.php');

$year = $_GET['year'] ?? null;

if ($year) {
    $stmt = $dbconn->prepare("SELECT * FROM kutipan_bulanan WHERE years = ?");
    $stmt->bind_param("s", $year);
    $stmt->execute();
    $result = $stmt->get_result();

    $monthNames = [
        1 => "JANUARI", 2 => "FEBRUARI", 3 => "MAC", 4 => "APRIL",
        5 => "MEI", 6 => "JUN", 7 => "JULAI", 8 => "OGOS",
        9 => "SEPTEMBER", 10 => "OKTOBER", 11 => "NOVEMBER", 12 => "DISEMBER"
    ];

    echo "<table class='table'><thead><tr><th>MONTH</th><th>AMOUNT</th></tr></thead><tbody>";
    while ($row = $result->fetch_assoc()) {
        $monthName = $monthNames[(int)$row['months']];
        $formattedAmount = number_format($row['amount']);
        echo "<tr><td>$monthName</td><td>$formattedAmount</td></tr>";
    }
    echo "</tbody></table>";

    $stmt->close();
} 
else {
    echo "Year not specified!";
}

$dbconn->close();
?>
</html>
