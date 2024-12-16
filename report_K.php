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
        <div class="section-title">PRESTASI BULANAN</div>
        <table>
            <thead>
                <tr>
                    <th>BULAN</th>
                    <?php
                        include('dbconn.php');
                        $yearRangeResult = $dbconn->query("SELECT MIN(years) AS min_year, MAX(years) AS max_year FROM kutipan_bulanan");
                        $yearRange = $yearRangeResult->fetch_assoc();
                        $minYear = $yearRange['min_year'];
                        $maxYear = $yearRange['max_year'];

                        for ($year = $minYear; $year <= $maxYear; $year++) {
                            echo "<th>$year</th>";
                        }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $months = [
                    1 => "JANUARI", 2 => "FEBRUARI", 3 => "MAC", 4 => "APRIL",
                    5 => "MEI", 6 => "JUN", 7 => "JULAI", 8 => "OGOS",
                    9 => "SEPTEMBER", 10 => "OKTOBER", 11 => "NOVEMBER", 12 => "DISEMBER"
                ];

                foreach ($months as $monthNumber => $monthName) {
                    echo "<tr>";
                    echo "<td class='table-header'>$monthName</td>";

                    for ($year = $minYear; $year <= $maxYear; $year++) {
                        $stmt = $dbconn->prepare("SELECT COALESCE(amount, 0) AS amount FROM kutipan_bulanan WHERE months = ? AND years = ?");
                            $stmt->bind_param("ii", $monthNumber, $year);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $amount = $result->fetch_assoc()['amount'] ?? 0;

                            $stmtTotal = $dbconn->prepare("SELECT COALESCE(SUM(amount), 1) AS total FROM kutipan_bulanan WHERE years = ?");
                            $stmtTotal->bind_param("i", $year);
                            $stmtTotal->execute();
                            $totalResult = $stmtTotal->get_result();
                            $total = $totalResult->fetch_assoc()['total'];

                            echo "<td>" . number_format($amount, 2) . "</td>";

                    }

                    echo "</tr>";
                }
                ?>
            </tbody>
            <tfoot>
                    <tr>
                        <td>TOTAL</td>
                        <?php
                        for ($year = $minYear; $year <= $maxYear; $year++) {
                            $stmtTotalYear = $dbconn->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM kutipan_bulanan WHERE years = ?");
                            $stmtTotalYear->bind_param("i", $year);
                            $stmtTotalYear->execute();
                            $totalResult = $stmtTotalYear->get_result();
                            $yearTotal = $totalResult->fetch_assoc()['total'];
                            echo "<td>" . number_format($yearTotal, 2) . "</td>";
                            $stmtTotalYear->close();
                        }
                        ?>
                    </tr>
                </tfoot>

        </table>

        <div class="section-title">KUTIPAN SUMBER</div>
        <table>
            <thead>
                <tr>
                    <th>SUMBER</th>
                        <?php
                        for ($year = $minYear; $year <= $maxYear; $year++) {
                            echo "<th>$year</th>";
                        }
                        ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $dbconn->prepare("
                SELECT DISTINCT c.category_name 
                FROM kutipan_sumber k
                JOIN category c ON k.category_id = c.category_id
                WHERE c.category_type = 'sumber' ");
                
                $stmt->execute();
                $sumberResult = $stmt->get_result();
                while ($sumber = $sumberResult->fetch_assoc()) {
                    $sourceName = $sumber['category_name'];
                    echo "<tr>";
                    echo "<td class='table-header'>$sourceName</td>";

                for ($year = $minYear; $year <= $maxYear; $year++) {
                            // Get total for the year
                            $stmtTotal = $dbconn->prepare("
                                SELECT COALESCE(SUM(amount), 1) AS total
                                FROM kutipan_sumber
                                WHERE years = ?
                            ");
                            $stmtTotal->bind_param("i", $year);
                            $stmtTotal->execute();
                            $totalResult = $stmtTotal->get_result();
                            $total = $totalResult->fetch_assoc()['total'];

                            // Get the specific source amount
                            $stmtAmount = $dbconn->prepare("
                                SELECT COALESCE(SUM(k.amount), 0) AS amount
                                FROM kutipan_sumber k
                                JOIN category c ON k.category_id = c.category_id
                                WHERE k.years = ? AND c.category_name = ?
                            ");
                            $stmtAmount->bind_param("is", $year, $sourceName);
                            $stmtAmount->execute();
                            $amountResult = $stmtAmount->get_result();
                            $amount = $amountResult->fetch_assoc()['amount'] ?? 0;

                            echo "<td>" . number_format($amount, 2) . "</td>";
                    }

                    echo "</tr>";
                }
                ?>
            </tbody>
            <tfoot>
                    <tr>
                        <td>TOTAL</td>
                        <?php
                        for ($year = $minYear; $year <= $maxYear; $year++) {
                            $stmtTotalYear = $dbconn->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM kutipan_sumber WHERE years = ?");
                            $stmtTotalYear->bind_param("i", $year);
                            $stmtTotalYear->execute();
                            $totalResult = $stmtTotalYear->get_result();
                            $yearTotal = $totalResult->fetch_assoc()['total'];
                            echo "<td>" . number_format($yearTotal, 2) . "</td>";
                            $stmtTotalYear->close();
                        }
                        ?>
                    </tr>
                </tfoot>
        </table>

        <div class="section-title">JENIS ZAKAT</div>
        <table>
            <thead>
                <tr>
                    <th>JENIS</th>
                        <?php
                        for ($year = $minYear; $year <= $maxYear; $year++) {
                            echo "<th>$year</th>";
                        }
                        ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $dbconn->prepare("
                SELECT DISTINCT c.category_name 
                FROM kutipan_jenis k
                JOIN category c ON k.category_id = c.category_id
                WHERE c.category_type = 'jenis kutipan' ");
                
                $stmt->execute();
                $sumberResult = $stmt->get_result();
                while ($sumber = $sumberResult->fetch_assoc()) {
                    $sourceName = $sumber['category_name'];
                    echo "<tr>";
                    echo "<td class='table-header'>$sourceName</td>";

                for ($year = $minYear; $year <= $maxYear; $year++) {
                            // Get total for the year
                            $stmtTotal = $dbconn->prepare("
                                SELECT COALESCE(SUM(amount), 1) AS total
                                FROM kutipan_jenis
                                WHERE years = ?
                            ");
                            $stmtTotal->bind_param("i", $year);
                            $stmtTotal->execute();
                            $totalResult = $stmtTotal->get_result();
                            $total = $totalResult->fetch_assoc()['total'];

                            // Get the specific source amount
                            $stmtAmount = $dbconn->prepare("
                                SELECT COALESCE(SUM(k.amount), 0) AS amount
                                FROM kutipan_jenis k
                                JOIN category c ON k.category_id = c.category_id
                                WHERE k.years = ? AND c.category_name = ?
                            ");
                            $stmtAmount->bind_param("is", $year, $sourceName);
                            $stmtAmount->execute();
                            $amountResult = $stmtAmount->get_result();
                            $amount = $amountResult->fetch_assoc()['amount'] ?? 0;

                            echo "<td>" . number_format($amount, 2) . "</td>";
                    }

                    echo "</tr>";
                }
                ?>
            </tbody>
            <tfoot>
                    <tr>
                        <td>TOTAL</td>
                        <?php
                        for ($year = $minYear; $year <= $maxYear; $year++) {
                            $stmtTotalYear = $dbconn->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM kutipan_jenis WHERE years = ?");
                            $stmtTotalYear->bind_param("i", $year);
                            $stmtTotalYear->execute();
                            $totalResult = $stmtTotalYear->get_result();
                            $yearTotal = $totalResult->fetch_assoc()['total'];
                            echo "<td>" . number_format($yearTotal, 2) . "</td>";
                            $stmtTotalYear->close();
                        }
                        ?>
                    </tr>
                </tfoot>
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
