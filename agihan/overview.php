<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agihan Zakat</title>
    <link rel="stylesheet" href="../css/overview.css">
</head>
<body>
    <div class="container">
        <a class="btn btn-secondary" href="../agihan/agihanMain.php" role="button">BACK</a>
        <a class="btn btn-secondary" onclick="generateReport()" role="button">GENERATE REPORT</a>
        <h2>Agihan Zakat</h2>
        <div class="scrollmenu">
            <table>
                <thead>
                    <tr>
                        <th>KATEGORI AGIHAN</th>
                        <?php
                        include('../dbconn.php');

                        // Get year range
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
                        WHERE c.category_type = 'agihan'
                    ");
                    $stmt->execute();
                    $sumberResult = $stmt->get_result();
                    
                    while ($sumber = $sumberResult->fetch_assoc()) {
                        $sourceName = $sumber['category_name'];
                        echo "<tr>";
                        echo "<td>$sourceName</td>";
                        for ($year = $minYear; $year <= $maxYear; $year++) {
                            $stmtAmount = $dbconn->prepare("
                                SELECT COALESCE(SUM(a.amount), 0) AS amount
                                FROM agihan_category a
                                JOIN category c ON a.category_id = c.category_id
                                WHERE a.years = ? AND c.category_name = ?
                            ");
                            $stmtAmount->bind_param("is", $year, $sourceName);
                            $stmtAmount->execute();
                            $amountResult = $stmtAmount->get_result();
                            $amount = $amountResult->fetch_assoc()['amount'] ?? 0;
                            echo "<td>" . number_format($amount, 2) . "</td>";
                            $stmtAmount->close();
                        }
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>

            <table>
                <thead>
                    <tr>
                        <th>KATEGORI ASNAF</th>
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
                        WHERE c.category_type = 'asnaf'
                    ");
                    $stmt->execute();
                    $jenisResult = $stmt->get_result();

                    while ($jenis = $jenisResult->fetch_assoc()) {
                        $jenisName = $jenis['category_name'];
                        echo "<tr>";
                        echo "<td>$jenisName</td>";
                        for ($year = $minYear; $year <= $maxYear; $year++) {
                            $stmtAmount = $dbconn->prepare("
                                SELECT COALESCE(SUM(a.amount), 0) AS amount
                                FROM agihan_asnaf a
                                JOIN category c ON a.category_id = c.category_id
                                WHERE a.years = ? AND c.category_name = ?
                            ");
                            $stmtAmount->bind_param("is", $year, $jenisName);
                            $stmtAmount->execute();
                            $amountResult = $stmtAmount->get_result();
                            $amount = $amountResult->fetch_assoc()['amount'] ?? 0;
                            echo "<td>" . number_format($amount, 2) . "</td>";
                            $stmtAmount->close();
                        }
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function generateReport() {
            window.location.href = '../agihan/generateReport.php';
        }
    </script>
</body>
</html>
