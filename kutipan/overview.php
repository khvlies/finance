<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/f-logo.png"  type="image/png">
    <title>Kutipan Zakat</title>
    <link rel="stylesheet" href="../css/overview.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <a class="btn btn-secondary" href="../kutipan/kutipanMain.php" role="button">BACK</a>
        <a href="../report_K.php" class="btn btn-secondary" role="button">DOWNLOAD</a>

        <h2>Kutipan Zakat</h2>
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
                        <th>PRESTASI BULANAN</th>
                        <?php
                        include('../dbconn.php');
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

                            // Calculate total for percentage
                            $stmtTotal = $dbconn->prepare("SELECT COALESCE(SUM(amount), 1) AS total FROM kutipan_bulanan WHERE years = ?");
                            $stmtTotal->bind_param("i", $year);
                            $stmtTotal->execute();
                            $totalResult = $stmtTotal->get_result();
                            $total = $totalResult->fetch_assoc()['total'];

                            $percentage = ($amount / $total) * 100;

                            echo "<td data-absolute='" . number_format($amount, 2) . "' data-percentage='" . number_format($percentage, 2) . "%'>" . number_format($amount, 2) . "</td>";
                            $stmt->close();
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
                            $stmtTotalYear = $dbconn->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM kutipan_bulanan WHERE years = ?");
                            $stmtTotalYear->bind_param("i", $year);
                            $stmtTotalYear->execute();
                            $totalResult = $stmtTotalYear->get_result();
                            $yearTotal = $totalResult->fetch_assoc()['total'];
                            echo "<th data-absolute='" . number_format($yearTotal, 2) . "' data-percentage='-'>" . number_format($yearTotal, 2) . "</th>";
                            $stmtTotalYear->close();
                        }
                        ?>
                    </tr>
                </tfoot>
            </table>

            <!-- KUTIPAN SUMBER Table -->
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
                    // Fetch all sources
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

                            // Calculate percentage
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
                            $stmtTotalSourceYear = $dbconn->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM kutipan_sumber WHERE years = ?");
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


            <!-- JENIS ZAKAT Table -->
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
                    // Fetch all types
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

                            // Get the specific type amount
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

                            // Calculate percentage
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
                            $stmtTotalTypeYear = $dbconn->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM kutipan_jenis WHERE years = ?");
                            $stmtTotalTypeYear->bind_param("i", $year);
                            $stmtTotalTypeYear->execute();
                            $totalResult = $stmtTotalTypeYear->get_result();
                            $yearTotal = $totalResult->fetch_assoc()['total'];
                            echo "<th data-absolute='" . number_format($yearTotal, 2) . "' data-percentage='-'>" . number_format($yearTotal, 2) . "</th>";
                            $stmtTotalTypeYear->close();
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
        <h3>MONTHLY PERFORMANCE</h3>
        <canvas id="monthlyChart"></canvas>

        <h3>SOURCE CONTRIBUTIONS</h3>
        <canvas id="sourceChart"></canvas>

        <h3>TYPE CONTRIBUTIONS</h3>
        <canvas id="typeChart"></canvas>
    </div>
    <?php
    $dataMonthly = [];
    $dataSource = [];
    $dataType = [];

    // Prepare monthly data
    foreach ($monthNames as $monthNumber => $monthName) {
        $dataMonthly[$monthName] = [];
        for ($year = $minYear; $year <= $maxYear; $year++) {
            $stmt = $dbconn->prepare("SELECT COALESCE(amount, 0) AS amount FROM kutipan_bulanan WHERE months = ? AND years = ?");
            $stmt->bind_param("ii", $monthNumber, $year);
            $stmt->execute();
            $result = $stmt->get_result();
            $dataMonthly[$monthName][$year] = $result->fetch_assoc()['amount'] ?? 0;
            $stmt->close();
        }
    }

    // Prepare source data
    $sumberResult = $dbconn->query("SELECT DISTINCT category_name FROM category WHERE category_type = 'sumber'");
    while ($sumber = $sumberResult->fetch_assoc()) {
        $sourceName = $sumber['category_name'];
        $dataSource[$sourceName] = [];
        for ($year = $minYear; $year <= $maxYear; $year++) {
            $stmt = $dbconn->prepare("SELECT COALESCE(SUM(amount), 0) AS amount FROM kutipan_sumber k JOIN category c ON k.category_id = c.category_id WHERE k.years = ? AND c.category_name = ?");
            $stmt->bind_param("is", $year, $sourceName);
            $stmt->execute();
            $result = $stmt->get_result();
            $dataSource[$sourceName][$year] = $result->fetch_assoc()['amount'] ?? 0;
            $stmt->close();
        }
    }

    // Prepare type data
    $jenisResult = $dbconn->query("SELECT DISTINCT category_name FROM category WHERE category_type = 'jenis kutipan'");
    while ($jenis = $jenisResult->fetch_assoc()) {
        $jenisName = $jenis['category_name'];
        $dataType[$jenisName] = [];
        for ($year = $minYear; $year <= $maxYear; $year++) {
            $stmt = $dbconn->prepare("SELECT COALESCE(SUM(amount), 0) AS amount FROM kutipan_jenis k JOIN category c ON k.category_id = c.category_id WHERE k.years = ? AND c.category_name = ?");
            $stmt->bind_param("is", $year, $jenisName);
            $stmt->execute();
            $result = $stmt->get_result();
            $dataType[$jenisName][$year] = $result->fetch_assoc()['amount'] ?? 0;
            $stmt->close();
        }
    }
    ?>


    <!-- Chart.js Script -->
    <script>
    // Ensure years start from 2017 for the charts
    const chartYears = <?= json_encode(range(2016, $maxYear)); ?>;

    // Filter the monthly, source, and type data for years starting from 2017
    const monthlyDataFiltered = Object.fromEntries(
        Object.entries(<?= json_encode($dataMonthly); ?>).map(([month, values]) => [
            month,
            Object.entries(values)
                .filter(([year]) => year >= 2016)
                .map(([_, value]) => value),
        ])
    );

    const sourceDataFiltered = Object.fromEntries(
        Object.entries(<?= json_encode($dataSource); ?>).map(([source, values]) => [
            source,
            Object.entries(values)
                .filter(([year]) => year >= 2016)
                .map(([_, value]) => value),
        ])
    );

    const typeDataFiltered = Object.fromEntries(
        Object.entries(<?= json_encode($dataType); ?>).map(([type, values]) => [
            type,
            Object.entries(values)
                .filter(([year]) => year >= 2016)
                .map(([_, value]) => value),
        ])
    );

    // Calculate percentage performance
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

    const percentageMonthly = calculatePercentageIncrease(monthlyDataFiltered);
    const percentageSource = calculatePercentageIncrease(sourceDataFiltered);
    const percentageType = calculatePercentageIncrease(typeDataFiltered);

    // Render percentage charts
    const createPercentageChart = (ctx, data, label) => {
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
                            label: (context) => `${context.raw}%`, // Show percentage sign
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => `${value}%`, // Show percentage sign
                        },
                    },
                },
            },
        });
    };

    // Render charts
    createPercentageChart(document.getElementById('monthlyChart'), percentageMonthly, "Monthly Performance Percentage");
    createPercentageChart(document.getElementById('sourceChart'), percentageSource, "Source Contributions Percentage");
    createPercentageChart(document.getElementById('typeChart'), percentageType, "Type Contributions Percentage");
</script>

</body>
</html>
