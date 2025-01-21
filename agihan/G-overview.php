<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/f-logo.png"  type="image/png">
    <title>Agihan Zakat</title>
    <link rel="stylesheet" href="../css/overview.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <a class="btn btn-secondary" href="../agihan/A-agihan.php" role="button">BACK</a>
        <a href="../report_G.php" class="btn btn-secondary" role="button">DOWNLOAD</a>

        <h2>Agihan Zakat</h2>
        <div class="scrollmenu">
            <div style="text-align: left; margin-bottom: 20px;">
                <label for="viewMode">Display Mode:</label>
                <select id="viewMode" onchange="toggleTableView()">
                    <option value="absolute">Absolute Amounts</option>
                    <option value="percentage">Percentage</option>
                </select>
            </div>
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
                    $agihanResult = $stmt->get_result();
                    
                    while ($agihan = $agihanResult->fetch_assoc()) {
                        $sourceName = $agihan['category_name'];
                        echo "<tr>";
                        echo "<td>$sourceName</td>";
                        for ($year = $minYear; $year <= $maxYear; $year++) {
                            $stmtTotal = $dbconn->prepare("
                                SELECT COALESCE(SUM(amount), 1) AS total
                                FROM agihan_category
                                WHERE years = ?
                            ");
                            $stmtTotal->bind_param("i", $year);
                            $stmtTotal->execute();
                            $totalResult = $stmtTotal->get_result();
                            $total = $totalResult->fetch_assoc()['total'];

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

                            $percentage = ($amount / $total) * 100;

                            echo "<td data-absolute='" . number_format($amount, 2) . "' data-percentage='" . number_format($percentage, 2) . "%'>" . number_format($amount, 2) . "</td>";
                            $stmtAmount->close();
                            $stmtTotal->close();
                        }
                        echo "</tr>";
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>TOTAL</th>
                        <?php
                        for ($year = $minYear; $year <= $maxYear; $year++) {
                            $stmtTotalSourceYear = $dbconn->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM agihan_category WHERE years = ?");
                            $stmtTotalSourceYear->bind_param("i", $year);
                            $stmtTotalSourceYear->execute();
                            $totalResult = $stmtTotalSourceYear->get_result();
                            $yearTotal = $totalResult->fetch_assoc()['total'];
                            echo "<th data-absolute='" . number_format($yearTotal, 2) . "' data-percentage='-'>" . number_format($yearTotal, 2) . "</th>";
                            $stmtTotalSourceYear->close();
                        }
                        ?>
                    </tr>
                </tfoot>
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
                    $asnafResult = $stmt->get_result();

                    while ($asnaf = $asnafResult->fetch_assoc()) {
                        $asnafName = $asnaf['category_name'];
                        echo "<tr>";
                        echo "<td>$asnafName</td>";
                        for ($year = $minYear; $year <= $maxYear; $year++) {
                            $stmtTotal = $dbconn->prepare("
                                SELECT COALESCE(SUM(amount), 1) AS total
                                FROM agihan_asnaf
                                WHERE years = ?
                            ");
                            $stmtTotal->bind_param("i", $year);
                            $stmtTotal->execute();
                            $totalResult = $stmtTotal->get_result();
                            $total = $totalResult->fetch_assoc()['total'];

                            $stmtAmount = $dbconn->prepare("
                                SELECT COALESCE(SUM(a.amount), 0) AS amount
                                FROM agihan_asnaf a
                                JOIN category c ON a.category_id = c.category_id
                                WHERE a.years = ? AND c.category_name = ?
                            ");
                            $stmtAmount->bind_param("is", $year, $asnafName);
                            $stmtAmount->execute();
                            $amountResult = $stmtAmount->get_result();
                            $amount = $amountResult->fetch_assoc()['amount'] ?? 0;

                            $percentage = ($amount / $total) * 100;

                            echo "<td data-absolute='" . number_format($amount, 2) . "' data-percentage='" . number_format($percentage, 2) . "%'>" . number_format($amount, 2) . "</td>";
                            $stmtAmount->close();
                            $stmtTotal->close();
                        }
                        echo "</tr>";
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>TOTAL</th>
                        <?php
                        for ($year = $minYear; $year <= $maxYear; $year++) {
                            $stmtTotalSourceYear = $dbconn->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM agihan_asnaf WHERE years = ?");
                            $stmtTotalSourceYear->bind_param("i", $year);
                            $stmtTotalSourceYear->execute();
                            $totalResult = $stmtTotalSourceYear->get_result();
                            $yearTotal = $totalResult->fetch_assoc()['total'];
                            echo "<th data-absolute='" . number_format($yearTotal, 2) . "' data-percentage='-'>" . number_format($yearTotal, 2) . "</th>";
                            $stmtTotalSourceYear->close();
                        }
                        ?>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <script>
        function toggleTableView() {
        const viewMode = document.getElementById("viewMode").value;
        const cells = document.querySelectorAll("table td[data-absolute][data-percentage]");

        cells.forEach(cell => {
            if (viewMode === "percentage") {
                cell.textContent = cell.getAttribute("data-percentage");
            } else {
                cell.textContent = cell.getAttribute("data-absolute");
            }
        });
    }
    </script>

    <div class="container" >
    <!-- Add Charts -->
        <h3>ZAKAT DISTRIBUTIONS</h3>
        <canvas id="agihanChart"></canvas>

        <h3>ASNAF DISTRIBUTIONS</h3>
        <canvas id="asnafChart"></canvas>

    </div>
    <?php
    $dataAgihan = [];
    $dataAsnaf = [];

    $agihanResult = $dbconn->query("SELECT DISTINCT category_name FROM category WHERE category_type = 'agihan'");
    while ($agihan = $agihanResult->fetch_assoc()) {
        $sourceName = $agihan['category_name'];
        $dataAgihan[$sourceName] = [];
        for ($year = $minYear; $year <= $maxYear; $year++) {
            $stmt = $dbconn->prepare("SELECT COALESCE(SUM(amount), 0) AS amount FROM agihan_category a JOIN category c ON a.category_id = c.category_id WHERE a.years = ? AND c.category_name = ?");
            $stmt->bind_param("is", $year, $sourceName);
            $stmt->execute();
            $result = $stmt->get_result();
            $dataAgihan[$sourceName][$year] = $result->fetch_assoc()['amount'] ?? 0;
            $stmt->close();
        }
    }

    $asnafResult = $dbconn->query("SELECT DISTINCT category_name FROM category WHERE category_type = 'asnaf'");
    while ($asnaf = $asnafResult->fetch_assoc()) {
        $sourceName = $asnaf['category_name'];
        $dataAsnaf[$sourceName] = [];
        for ($year = $minYear; $year <= $maxYear; $year++) {
            $stmt = $dbconn->prepare("SELECT COALESCE(SUM(amount), 0) AS amount FROM agihan_asnaf a JOIN category c ON a.category_id = c.category_id WHERE a.years = ? AND c.category_name = ?");
            $stmt->bind_param("is", $year, $sourceName);
            $stmt->execute();
            $result = $stmt->get_result();
            $dataAsnaf[$sourceName][$year] = $result->fetch_assoc()['amount'] ?? 0;
            $stmt->close();
        }
    }
    ?>

    <script>
        const chartYears = <?= json_encode(range(2016, $maxYear)); ?>;

        // Filtered data for Agihan
        const agihanDataFiltered = Object.fromEntries(
            Object.entries(<?= json_encode($dataAgihan); ?>).map(([source, values]) => [
                source,
                chartYears.map((year) => values[year] || 0),
            ])
        );

        // Filtered data for Asnaf
        const asnafDataFiltered = Object.fromEntries(
            Object.entries(<?= json_encode($dataAsnaf); ?>).map(([source, values]) => [
                source,
                chartYears.map((year) => values[year] || 0),
            ])
        );

        // Calculate percentage increase
        const calculatePercentageIncrease = (data) => {
            let percentageData = {};
            Object.entries(data).forEach(([key, values]) => {
                percentageData[key] = [];
                for (let i = 1; i < values.length; i++) {
                    const prevValue = values[i - 1];
                    const currValue = values[i];
                    const percentageIncrease = prevValue > 0 ? ((currValue - prevValue) / prevValue) * 100 : 0;
                    percentageData[key].push(percentageIncrease.toFixed(2)); // Round to 2 decimal places
                }
            });
            return percentageData;
        };

        // Percentage data
        const percentageAgihan = calculatePercentageIncrease(agihanDataFiltered);
        const percentageAsnaf = calculatePercentageIncrease(asnafDataFiltered);

        // Function to create a chart
        const createPercentageChart = (ctx, data, percentageData, label) => {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartYears.slice(1), // Skip the first year for percentage performance
                    datasets: Object.entries(data).map(([key, values], index) => ({
                        label: key,
                        data: values,
                        borderColor: `hsl(${index * 40}, 70%, 50%)`,
                        fill: false,
                    })),
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: (context) => {
                                const yearIndex = context.dataIndex;
                                const amount = context.raw;
                                const percentage =
                                    percentageData[context.dataset.label] &&
                                    percentageData[context.dataset.label][yearIndex - 1];
                                return percentage !== undefined
                                    ? `${context.dataset.label}: RM${amount.toLocaleString()} (${percentage}%)`
                                    : `${context.dataset.label}: RM${amount.toLocaleString()}`;
                                },
                            },
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Amount (RM)',
                            },
                        },
                    },
                },
            });
        };

        // Create charts
        createPercentageChart(
            document.getElementById('agihanChart'),
            agihanDataFiltered, 
            percentageAgihan, 
            "Zakat Distributions Percentage"
        );
        createPercentageChart(
            document.getElementById('asnafChart'),
            asnafDataFiltered, 
            percentageAsnaf, 
            "Asnaf Distributions Percentage"
        );

    </script>    
    
</body>
</html>
