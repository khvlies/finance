<?php
include('dbconn.php');

$year = $_GET['year'] ?? null;

if ($year) {
    $stmt = $dbconn->prepare("SELECT * FROM kutipan_jenis WHERE years = ?");
    $stmt->bind_param("s", $year);
    $stmt->execute();
    $result = $stmt->get_result();

    // Category mapping
    $typekutipan = [
        33 => "Pendapatan", 34 => "Perniagaan", 35 => "Harta", 36 => "Simpanan",
        37 => "Saham", 38 => "KWSP", 39 => "Tanaman", 40 => "Emas",
        41 => "Ternakan", 42 => "Perak", 43 => "Fitrah"
    ];


    echo "<table class='table'><thead><tr><th>Type</th><th>Amount</th></tr></thead><tbody>";
    while ($row = $result->fetch_assoc()) {
        $zakatType = isset($typekutipan[(int)$row['category_id']]) 
            ? $typekutipan[(int)$row['category_id']] 
            : "Unknown";
        $formattedAmount = number_format($row['amount']);
        echo "<tr><td>$zakatType</td><td>$formattedAmount</td></tr>";
    }
    echo "</tbody></table>";

    $stmt->close();
}
else {
    echo "Year not specified!";
    }
    $dbconn->close();
?>
