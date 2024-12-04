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
            <?php
            include('../dbconn.php');

            // Get year range from database
            $yearRangeResult = $dbconn->query("SELECT MIN(years) AS min_year, MAX(years) AS max_year FROM kutipan_bulanan");
            $yearRange = $yearRangeResult->fetch_assoc();
            $minYear = $yearRange['min_year'];
            $maxYear = $yearRange['max_year'];

            // Define month names
            $monthNames = [
                1 => "JANUARI", 2 => "FEBRUARI", 3 => "MAC", 4 => "APRIL",
                5 => "MEI", 6 => "JUN", 7 => "JULAI", 8 => "OGOS",
                9 => "SEPTEMBER", 10 => "OKTOBER", 11 => "NOVEMBER", 12 => "DISEMBER"
            ];

            // Define sections
            $sections = [
                "PRESTASI BULANAN" => [
                    "query" => "SELECT months, years, COALESCE(amount, 0) AS amount FROM kutipan_bulanan WHERE years BETWEEN ? AND ?",
                    "group_by" => "months",
                    "label" => $monthNames,
                    "value_key" => "amount"
                ],
                "JENIS ZAKAT" => [
                    "query" => "SELECT k.category_id, c.category_name, k.years, COALESCE(k.amount, 0) AS amount
                                FROM kutipan_jenis k
                                JOIN category c ON k.category_id = c.category_id
                                WHERE c.category_type = 'jenis kutipan' AND k.years BETWEEN ? AND ?",
                    "group_by" => "category_id",
                    "label_key" => "category_name",
                    "value_key" => "amount"
                ],
                "SUMBER ZAKAT" => [
                    "query" => "SELECT k.category_id, c.category_name, k.years, COALESCE(k.amount, 0) AS amount
                                FROM kutipan_sumber k
                                JOIN category c ON k.category_id = c.category_id
                                WHERE c.category_type = 'sumber' AND k.years BETWEEN ? AND ?",
                    "group_by" => "category_id",
                    "label_key" => "category_name",
                    "value_key" => "amount"
                ]
            ];

            foreach ($sections as $title => $section) {
                $stmt = $dbconn->prepare($section['query']);
                $stmt->bind_param('ii', $minYear, $maxYear);
                $stmt->execute();
                $result = $stmt->get_result();

                // Prepare data
                $data = [];
                $labels = isset($section['label']) ? $section['label'] : [];
                while ($row = $result->fetch_assoc()) {
                    $data[$row[$section['group_by']]][$row['years']] = $row[$section['value_key']];
                    if (!isset($labels[$row[$section['group_by']]]) && isset($row[$section['label_key']])) {
                        $labels[$row[$section['group_by']]] = $row[$section['label_key']];
                    }
                }

                // Render table
                echo "<table>";
                echo "<thead><tr><th>$title</th>";
                for ($year = $minYear; $year <= $maxYear; $year++) {
                    echo "<th>$year</th>";
                }
                echo "</tr></thead><tbody>";

                foreach ($labels as $key => $label) {
                    echo "<tr><td>$label</td>";
                    for ($year = $minYear; $year <= $maxYear; $year++) {
                        $amount = isset($data[$key][$year]) ? number_format($data[$key][$year], 2) : '0.00';
                        echo "<td>$amount</td>";
                    }
                    echo "</tr>";
                }

                echo "<tfoot><tr><td>TOTAL</td>";
                for ($year = $minYear; $year <= $maxYear; $year++) {
                    $yearTotal = array_sum(array_column($data, $year));
                    echo "<td>" . number_format($yearTotal, 2) . "</td>";
                }
                echo "</tr></tfoot></tbody></table>";
            }

            $dbconn->close();
            ?>
        </div>
    </div>
</body>
</html>
