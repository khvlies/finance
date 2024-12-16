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
        <title>Agihan Zakat Report</title>
        <link rel="stylesheet" href="../css/report.css">
    </head>
    <body>
        <h1>Agihan Zakat</h1>
        <div class="section-title">KATEGORI AGIHAN</div>
        <table>
            <thead>
                <tr>
                    <th>KATEGORI</th>
                        <?php
                        include('dbconn.php');
                        $yearRangeResult = $dbconn->query("SELECT MIN(years) AS min_year, MAX(years) AS max_year FROM agihan_category");
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
                $stmt = $dbconn->prepare("
                SELECT DISTINCT c.category_name 
                FROM agihan_category a
                JOIN category c ON a.category_id = c.category_id
                WHERE c.category_type = 'agihan' ");
                
                $stmt->execute();
                $agihanResult = $stmt->get_result();
                while ($agihan = $agihanResult->fetch_assoc()) {
                    $category = $agihan['category_name'];
                    echo "<tr>";
                    echo "<td class='table-header'>$category</td>";

                for ($year = $minYear; $year <= $maxYear; $year++) {
                            // Get total for the year
                            $stmtTotal = $dbconn->prepare("
                                SELECT COALESCE(SUM(amount), 1) AS total
                                FROM agihan_category
                                WHERE years = ?
                            ");
                            $stmtTotal->bind_param("i", $year);
                            $stmtTotal->execute();
                            $totalResult = $stmtTotal->get_result();
                            $total = $totalResult->fetch_assoc()['total'];

                            // Get the specific source amount
                            $stmtAmount = $dbconn->prepare("
                                SELECT COALESCE(SUM(a.amount), 0) AS amount
                                FROM agihan_category a
                                JOIN category c ON a.category_id = c.category_id
                                WHERE a.years = ? AND c.category_name = ?
                            ");
                            $stmtAmount->bind_param("is", $year, $category);
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
                            $stmtTotalYear = $dbconn->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM agihan_category WHERE years = ?");
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

        <div class="section-title">AGIHAN ASNAF</div>
        <table>
            <thead>
                <tr>
                    <th>KATEGORI</th>
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
                FROM agihan_asnaf a
                JOIN category c ON a.category_id = c.category_id
                WHERE c.category_type = 'asnaf' ");
                
                $stmt->execute();
                $agihanResult = $stmt->get_result();
                while ($agihan = $agihanResult->fetch_assoc()) {
                    $category = $agihan['category_name'];
                    echo "<tr>";
                    echo "<td class='table-header'>$category</td>";

                for ($year = $minYear; $year <= $maxYear; $year++) {
                            // Get total for the year
                            $stmtTotal = $dbconn->prepare("
                                SELECT COALESCE(SUM(amount), 1) AS total
                                FROM agihan_asnaf
                                WHERE years = ?
                            ");
                            $stmtTotal->bind_param("i", $year);
                            $stmtTotal->execute();
                            $totalResult = $stmtTotal->get_result();
                            $total = $totalResult->fetch_assoc()['total'];

                            // Get the specific source amount
                            $stmtAmount = $dbconn->prepare("
                                SELECT COALESCE(SUM(a.amount), 0) AS amount
                                FROM agihan_asnaf a
                                JOIN category c ON a.category_id = c.category_id
                                WHERE a.years = ? AND c.category_name = ?
                            ");
                            $stmtAmount->bind_param("is", $year, $category);
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
                            $stmtTotalYear = $dbconn->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM agihan_asnaf WHERE years = ?");
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
    $mpdf->Output('Agihan_Zakat_Report.pdf', 'I');
} catch (\Mpdf\MpdfException $e) {
    echo "An error occurred: " . $e->getMessage();
}
