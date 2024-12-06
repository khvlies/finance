<?php
include('../dbconn.php');

$year = $_GET['year'] ?? null;

if ($year) {
    $stmt = $dbconn->prepare("
        SELECT a.amount, c.category_name 
        FROM agihan_category a
        JOIN category c ON a.category_id = c.category_id
        WHERE a.years = ? AND c.category_type = 'agihan'
    ");
    $stmt->bind_param("s", $year);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<table class='table'>
            <thead>
            <tr>
                <th>TYPE</th>
                <th>AMOUNT</th>
            </tr>
            </thead>
            <tbody>";

    while ($row = $result->fetch_assoc()) {
        $formattedAmount = number_format($row['amount']);
        echo "<tr>
                <td>{$row['category_name']}</td>
                <td>{$formattedAmount}</td>
              </tr>";
    }

    echo "</tbody></table>";

    $stmt->close();
} else {
    echo "Year not specified!";
}

$dbconn->close();
?>
