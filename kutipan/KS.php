<?php
include('../dbconn.php');

$year = $_GET['year'] ?? null;

if ($year) {
    // Update the query to include only 'jenis kutipan' categories
    $stmt = $dbconn->prepare("
    SELECT COALESCE(k.amount, 0) AS amount, c.category_name 
    FROM kutipan_sumber k
    JOIN category c ON k.category_id = c.category_id
    WHERE k.years = ? AND c.category_type = 'sumber'
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
