<?php
require_once __DIR__ . '/vendor/autoload.php'; // Include mPDF

use Mpdf\Mpdf;

try {
    // Initialize mPDF
    $mpdf = new Mpdf();

    // Start output buffering
    ob_start();
    ?>
    <!-- HTML Content -->
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Kewangan Amil Report</title>
        <link rel="stylesheet" href="../css/report.css">
    </head>
    <body>
        <h1>Kewangan Amil</h1>
        <div class="section-title">PENDAPATAN AMIL</div>
        <table>
            <thead>
                <tr>
                    <th>KATEGORI</th>
                    <?php
                    include('dbconn.php');

                    // Prepare and execute statement for categories
                    $stmt = $dbconn->prepare("SELECT DISTINCT c.category_name 
                                            FROM amil_income m
                                            JOIN category c ON m.category_id = c.category_id
                                            WHERE c.category_type = 'pendapatan'");
                    $stmt->execute();
                    $incomeResult = $stmt->get_result();

                    $categories = [];
                    while ($income = $incomeResult->fetch_assoc()) {
                        $category = $income['category_name'];
                        $categories[] = $category; // Store category names for later use
                        echo "<th>$category</th>";
                    }
                    echo "<th>TOTAL</th>"; // Add total header at the end
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch the range of years
                $yearRangeResult = $dbconn->query("SELECT MIN(years) AS min_year, MAX(years) AS max_year FROM amil_income");
                $yearRange = $yearRangeResult->fetch_assoc();
                $minYear = $yearRange['min_year'];
                $maxYear = $yearRange['max_year'];

                // Loop through each year
                for ($year = $minYear; $year <= $maxYear; $year++) {
                    echo "<tr>";
                    echo "<td>$year</td>";

                    $yearTotal = 0; // Initialize year total

                    // Loop through each category and calculate the amount for the year
                    foreach ($categories as $category) {
                        $stmtAmount = $dbconn->prepare("SELECT COALESCE(SUM(m.amount), 0) AS amount
                                                    FROM amil_income m
                                                    JOIN category c ON m.category_id = c.category_id
                                                    WHERE m.years = ? AND c.category_name = ?");
                        $stmtAmount->bind_param("is", $year, $category);
                        $stmtAmount->execute();
                        $amountResult = $stmtAmount->get_result();
                        $amount = $amountResult->fetch_assoc()['amount'] ?? 0;
                        $yearTotal += $amount; // Add to year total
                        echo "<td>" . number_format($amount, 2) . "</td>";
                    }

                    // Print year total at the end of the row
                    echo "<td>" . number_format($yearTotal, 2) . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
            <!-- <tfoot>
                <tr>
                    <td>TOTAL</td>
                    <?php
                    $grandTotal = 0; // Initialize grand total

                    // Loop through each category to calculate grand totals
                    foreach ($categories as $category) {
                        $stmtTotalCategory = $dbconn->prepare("SELECT COALESCE(SUM(m.amount), 0) AS total
                                                            FROM amil_income m
                                                            JOIN category c ON m.category_id = c.category_id
                                                            WHERE c.category_name = ?");
                        $stmtTotalCategory->bind_param("s", $category);
                        $stmtTotalCategory->execute();
                        $totalResult = $stmtTotalCategory->get_result();
                        $categoryTotal = $totalResult->fetch_assoc()['total'] ?? 0;
                        $grandTotal += $categoryTotal; // Add to grand total
                        echo "<td>" . number_format($categoryTotal, 2) . "</td>";
                        $stmtTotalCategory->close();
                    }

                    // Print grand total at the end
                    echo "<td>" . number_format($grandTotal, 2) . "</td>";
                    ?>
                </tr>
            </tfoot> -->
        </table>

        <br>
        <div class="section-title">PERBELANJAAN AMIL</div>
        <table>
            <thead>
                <tr>
                    <th>KATEGORI</th>
                    <?php

                    // Prepare and execute statement for categories
                    $stmt = $dbconn->prepare("SELECT DISTINCT c.category_name 
                                            FROM amil_expense m
                                            JOIN category c ON m.category_id = c.category_id
                                            WHERE c.category_type = 'perbelanjaan'");
                    $stmt->execute();
                    $expenseResult = $stmt->get_result();

                    $categories = [];
                    while ($expense = $expenseResult->fetch_assoc()) {
                        $category = $expense['category_name'];
                        $categories[] = $category; // Store category names for later use
                        echo "<th>$category</th>";
                    }
                    echo "<th>TOTAL</th>"; // Add total header at the end
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch the range of years
                $yearRangeResult = $dbconn->query("SELECT MIN(years) AS min_year, MAX(years) AS max_year FROM amil_expense");
                $yearRange = $yearRangeResult->fetch_assoc();
                $minYear = $yearRange['min_year'];
                $maxYear = $yearRange['max_year'];

                // Loop through each year
                for ($year = $minYear; $year <= $maxYear; $year++) {
                    echo "<tr>";
                    echo "<td>$year</td>";

                    $yearTotal = 0; // Initialize year total

                    // Loop through each category and calculate the amount for the year
                    foreach ($categories as $category) {
                        $stmtAmount = $dbconn->prepare("SELECT COALESCE(SUM(m.amount), 0) AS amount
                                                    FROM amil_expense m
                                                    JOIN category c ON m.category_id = c.category_id
                                                    WHERE m.years = ? AND c.category_name = ?");
                        $stmtAmount->bind_param("is", $year, $category);
                        $stmtAmount->execute();
                        $amountResult = $stmtAmount->get_result();
                        $amount = $amountResult->fetch_assoc()['amount'] ?? 0;
                        $yearTotal += $amount; // Add to year total
                        echo "<td>" . number_format($amount, 2) . "</td>";
                    }

                    // Print year total at the end of the row
                    echo "<td>" . number_format($yearTotal, 2) . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
            <!-- <tfoot>
                <tr>
                    <td>TOTAL</td>
                    <?php
                    $grandTotal = 0; // Initialize grand total

                    // Loop through each category to calculate grand totals
                    foreach ($categories as $category) {
                        $stmtTotalCategory = $dbconn->prepare("SELECT COALESCE(SUM(m.amount), 0) AS total
                                                            FROM amil_expense m
                                                            JOIN category c ON m.category_id = c.category_id
                                                            WHERE c.category_name = ?");
                        $stmtTotalCategory->bind_param("s", $category);
                        $stmtTotalCategory->execute();
                        $totalResult = $stmtTotalCategory->get_result();
                        $categoryTotal = $totalResult->fetch_assoc()['total'] ?? 0;
                        $grandTotal += $categoryTotal; // Add to grand total
                        echo "<td>" . number_format($categoryTotal, 2) . "</td>";
                        $stmtTotalCategory->close();
                    }

                    // Print grand total at the end
                    echo "<td>" . number_format($grandTotal, 2) . "</td>";
                    ?>
                </tr>
            </tfoot> -->
        </table>

    </body>
    </html>
    <?php
    // Capture the HTML
    $html = ob_get_clean();

    // Write HTML to PDF
    $mpdf->WriteHTML($html);

    // Output the PDF
    $mpdf->Output('Kewangan_Amil_Report.pdf', 'I');
} catch (\Mpdf\MpdfException $e) {
    echo "An error occurred: " . $e->getMessage();
}
