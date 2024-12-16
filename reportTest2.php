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
        <title>Kutipan Zakat Report</title>
        <link rel="stylesheet" href="../css/report.css">
    </head>
    <body>
        <h1>Kutipan Zakat</h1>
        <div class="section-title">KUTIPAN BULANAN</div>
        <table>
            <thead>
                <tr>
                    <th>TAHUN</th>
                    <?php
                    include('dbconn.php');

                    $monthNames = [
                        1 => "JANUARI", 2 => "FEBRUARI", 3 => "MAC", 4 => "APRIL",
                        5 => "MEI", 6 => "JUN", 7 => "JULAI", 8 => "OGOS",
                        9 => "SEPTEMBER", 10 => "OKTOBER", 11 => "NOVEMBER", 12 => "DISEMBER"
                    ];

                    foreach ($monthNames as $monthName) {
                        echo "<th>$monthName</th>";
                    }
                    echo "<th>TOTAL</th>"; // Add total header at the end
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch the range of years
                $yearRangeResult = $dbconn->query("SELECT MIN(years) AS min_year, MAX(years) AS max_year FROM kutipan_bulanan");
                $yearRange = $yearRangeResult->fetch_assoc();
                $minYear = $yearRange['min_year'];
                $maxYear = $yearRange['max_year'];

                // Loop through each year
                for ($year = $minYear; $year <= $maxYear; $year++) {
                    echo "<tr>";
                    echo "<td class='table-header'>$year</td>";

                    $yearTotal = 0; // Initialize year total

                    // Loop through each month
                    foreach ($monthNames as $monthNumber => $monthName) {
                        $stmt = $dbconn->prepare("SELECT COALESCE(amount, 0) AS amount FROM kutipan_bulanan WHERE months = ? AND years = ?");
                        $stmt->bind_param("ii", $monthNumber, $year);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $amount = $result->fetch_assoc()['amount'] ?? 0;

                        $yearTotal += $amount; // Add to year total
                        echo "<td>" . number_format($amount, 2) . "</td>";
                        $stmt->close();
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
                    // Loop through each month to calculate grand totals
                    foreach ($monthNames as $monthNumber => $monthName) {
                        $stmtTotalMonth = $dbconn->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM kutipan_bulanan WHERE months = ?");
                        $stmtTotalMonth->bind_param("i", $monthNumber);
                        $stmtTotalMonth->execute();
                        $totalResult = $stmtTotalMonth->get_result();
                        $monthTotal = $totalResult->fetch_assoc()['total'];
                        echo "<td>" . number_format($monthTotal, 2) . "</td>";
                        $stmtTotalMonth->close();
                    }

                    // Calculate and print grand total
                    $stmtGrandTotal = $dbconn->query("SELECT COALESCE(SUM(amount), 0) AS grand_total FROM kutipan_bulanan");
                    $grandTotal = $stmtGrandTotal->fetch_assoc()['grand_total'];
                    echo "<td>" . number_format($grandTotal, 2) . "</td>";
                    ?>
                </tr>
            </tfoot> -->
        </table>
        <br>

                <div class="section-title">KUTIPAN SUMBER</div>
                <table>
                    <thead>
                        <tr>
                            <th>KATEGORI</th>
                            <?php

                            // Prepare and execute statement for categories
                            $stmt = $dbconn->prepare("SELECT DISTINCT c.category_name 
                                                    FROM kutipan_sumber k
                                                    JOIN category c ON k.category_id = c.category_id
                                                    WHERE c.category_type = 'sumber'");
                            $stmt->execute();
                            $agihanResult = $stmt->get_result();

                            $categories = [];
                            while ($agihan = $agihanResult->fetch_assoc()) {
                                $category = $agihan['category_name'];
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
                        $yearRangeResult = $dbconn->query("SELECT MIN(years) AS min_year, MAX(years) AS max_year FROM kutipan_sumber");
                        $yearRange = $yearRangeResult->fetch_assoc();
                        $minYear = $yearRange['min_year'];
                        $maxYear = $yearRange['max_year'];

                        // Loop through each year
                        for ($year = $minYear; $year <= $maxYear; $year++) {
                            echo "<tr>";
                            echo "<td class='table-header'>$year</td>";

                            $yearTotal = 0; // Initialize year total

                            // Loop through each category and calculate the amount for the year
                            foreach ($categories as $category) {
                                $stmtAmount = $dbconn->prepare("SELECT COALESCE(SUM(k.amount), 0) AS amount
                                                            FROM kutipan_sumber k
                                                            JOIN category c ON k.category_id = c.category_id
                                                            WHERE k.years = ? AND c.category_name = ?");
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
                                $stmtTotalCategory = $dbconn->prepare("SELECT COALESCE(SUM(k.amount), 0) AS total
                                                                    FROM kutipan_sumber k
                                                                    JOIN category c ON k.category_id = c.category_id
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
        <div class="section-title">KUTIPAN JENIS</div>
        <table>
            <thead>
                <tr>
                    <th>KATEGORI</th>
                    <?php

                    // Prepare and execute statement for categories
                    $stmt = $dbconn->prepare("SELECT DISTINCT c.category_name 
                                            FROM kutipan_jenis k
                                            JOIN category c ON k.category_id = c.category_id
                                            WHERE c.category_type = 'jenis kutipan'");
                    $stmt->execute();
                    $agihanResult = $stmt->get_result();

                    $categories = [];
                    while ($agihan = $agihanResult->fetch_assoc()) {
                        $category = $agihan['category_name'];
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
                $yearRangeResult = $dbconn->query("SELECT MIN(years) AS min_year, MAX(years) AS max_year FROM kutipan_jenis");
                $yearRange = $yearRangeResult->fetch_assoc();
                $minYear = $yearRange['min_year'];
                $maxYear = $yearRange['max_year'];

                // Loop through each year
                for ($year = $minYear; $year <= $maxYear; $year++) {
                    echo "<tr>";
                    echo "<td class='table-header'>$year</td>";

                    $yearTotal = 0; // Initialize year total

                    // Loop through each category and calculate the amount for the year
                    foreach ($categories as $category) {
                        $stmtAmount = $dbconn->prepare("SELECT COALESCE(SUM(k.amount), 0) AS amount
                                                    FROM kutipan_jenis k
                                                    JOIN category c ON k.category_id = c.category_id
                                                    WHERE k.years = ? AND c.category_name = ?");
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
                        $stmtTotalCategory = $dbconn->prepare("SELECT COALESCE(SUM(k.amount), 0) AS total
                                                            FROM kutipan_jenis k
                                                            JOIN category c ON k.category_id = c.category_id
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
    $mpdf->Output('Kutipan_Zakat_Report.pdf', 'I');
} catch (\Mpdf\MpdfException $e) {
    echo "An error occurred: " . $e->getMessage();
}
