<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kutipan Zakat</title>
    <link rel="stylesheet" href="../css/overview.css">
</head>
<body>
    <div class="container">
        <a class="btn btn-secondary" href="../kutipan/A-kutipan.php" role="button">BACK</a>
        <h2>Kutipan Zakat</h2>
        <div class="scrollmenu">

            <!-- PRESTASI BULANAN Section -->
            <table>
                <thead>
                    <tr>
                        <th>PRESTASI BULANAN</th>
                        <?php
                        include('../dbconn.php');

                        // Get year range
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
                    $monthNames = [
                        1 => "JANUARI", 2 => "FEBRUARI", 3 => "MAC", 4 => "APRIL",
                        5 => "MEI", 6 => "JUN", 7 => "JULAI", 8 => "OGOS",
                        9 => "SEPTEMBER", 10 => "OKTOBER", 11 => "NOVEMBER", 12 => "DISEMBER"
                    ];

                    foreach ($monthNames as $monthNumber => $monthName) {
                        echo "<tr>";
                        echo "<td>$monthName</td>";
                        for ($year = $minYear; $year <= $maxYear; $year++) {
                            $stmt = $dbconn->prepare("SELECT COALESCE(amount, 0) AS amount FROM kutipan_bulanan WHERE months = ? AND years = ?");
                            $stmt->bind_param("ii", $monthNumber, $year);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $amount = $result->fetch_assoc()['amount'] ?? 0;
                            echo "<td>" . number_format($amount, 2) . "</td>";
                            $stmt->close();
                        }
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- KUTIPAN SUMBER Section -->
            <table>
                <thead>
                    <tr>
                        <th>KUTIPAN SUMBER</th>
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
                        WHERE c.category_type = 'sumber'
                    ");
                    $stmt->execute();
                    $sumberResult = $stmt->get_result();
                    
                    while ($sumber = $sumberResult->fetch_assoc()) {
                        $sourceName = $sumber['category_name'];
                        echo "<tr>";
                        echo "<td>$sourceName</td>";
                        for ($year = $minYear; $year <= $maxYear; $year++) {
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
                            $stmtAmount->close();
                        }
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- JENIS ZAKAT Section -->
            <table>
                <thead>
                    <tr>
                        <th>JENIS ZAKAT</th>
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
                        WHERE c.category_type = 'jenis kutipan'
                    ");
                    $stmt->execute();
                    $jenisResult = $stmt->get_result();

                    while ($jenis = $jenisResult->fetch_assoc()) {
                        $jenisName = $jenis['category_name'];
                        echo "<tr>";
                        echo "<td>$jenisName</td>";
                        for ($year = $minYear; $year <= $maxYear; $year++) {
                            $stmtAmount = $dbconn->prepare("
                                SELECT COALESCE(SUM(k.amount), 0) AS amount
                                FROM kutipan_jenis k
                                JOIN category c ON k.category_id = c.category_id
                                WHERE k.years = ? AND c.category_name = ?
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
</body>
</html>
