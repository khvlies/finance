<?php
include('../dbconn.php');

$year = $_GET['year'] ?? null;

if ($year) {
    $stmt = $dbconn->prepare("
        SELECT m.amount, c.category_name 
        FROM amil_income m
        JOIN category c ON m.category_id = c.category_id
        WHERE m.years = ? AND c.category_type = 'pendapatan'
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
        $amount = $row['amount'] ?? 0; // Default to 0 if amount is null
        $formattedAmount = number_format($amount);
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
