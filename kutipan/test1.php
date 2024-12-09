<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kutipan Zakat</title>
    <link rel="stylesheet" href="../css/overview.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <a class="btn btn-secondary" href="../kutipan/kutipanMain.php" role="button">BACK</a>
        <h2>Kutipan Zakat</h2>
        <div class="scrollmenu">

            <!-- PHP Data Extraction -->
            <?php
            include('../dbconn.php');

            // Fetch years for the range
            $yearRangeResult = $dbconn->query("SELECT MIN(years) AS min_year, MAX(years) AS max_year FROM kutipan_bulanan");
            $yearRange = $yearRangeResult->fetch_assoc();
            $minYear = $yearRange['min_year'];
            $maxYear = $yearRange['max_year'];

            // Fetch data for charts
            $dataMonthly = [];
            $dataSource = [];
            $dataType = [];
            
            // Monthly Data
            $monthNames = [1 => "JANUARI", 2 => "FEBRUARI", 3 => "MAC", 4 => "APRIL", 5 => "MEI", 6 => "JUN", 7 => "JULAI", 8 => "OGOS", 9 => "SEPTEMBER", 10 => "OKTOBER", 11 => "NOVEMBER", 12 => "DISEMBER"];
            foreach ($monthNames as $monthNumber => $monthName) {
                $dataMonthly[$monthName] = [];
                for ($year = $minYear; $year <= $maxYear; $year++) {
                    $stmt = $dbconn->prepare("SELECT COALESCE(amount, 0) AS amount FROM kutipan_bulanan WHERE months = ? AND years = ?");
                    $stmt->bind_param("ii", $monthNumber, $year);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $amount = $result->fetch_assoc()['amount'] ?? 0;
                    $dataMonthly[$monthName][$year] = $amount;
                    $stmt->close();
                }
            }

            // Source Data
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
                $dataSource[$sourceName] = [];
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
                    $dataSource[$sourceName][$year] = $amount;
                    $stmtAmount->close();
                }
            }

            // Type Data
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
                $dataType[$jenisName] = [];
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
                    $dataType[$jenisName][$year] = $amount;
                    $stmtAmount->close();
                }
            }
            ?>

            <!-- Charts -->
            <h3>Monthly Performance</h3>
            <canvas id="monthlyChart"></canvas>

            <h3>Source Contributions</h3>
            <canvas id="sourceChart"></canvas>

            <h3>Type Contributions</h3>
            <canvas id="typeChart"></canvas>

        </div>
    </div>

    <!-- Chart.js Script -->
    <script>
        const years = <?= json_encode(range($minYear, $maxYear)); ?>;

        // Data Preparation
        const monthlyData = <?= json_encode($dataMonthly); ?>;
        const sourceData = <?= json_encode($dataSource); ?>;
        const typeData = <?= json_encode($dataType); ?>;

        const calculatePercentageIncrease = (data) => {
            let result = {};
            for (const key in data) {
                const values = Object.values(data[key]);
                result[key] = [];
                for (let i = 1; i < values.length; i++) {
                    const percentageIncrease = ((values[i] - values[i - 1]) / values[i - 1]) * 100 || 0;
                    result[key].push(percentageIncrease.toFixed(2));
                }
            }
            return result;
        };

        // Prepare Percentage Data
        const percentageMonthly = calculatePercentageIncrease(monthlyData);
        const percentageSource = calculatePercentageIncrease(sourceData);
        const percentageType = calculatePercentageIncrease(typeData);

        // Chart.js Configurations
        const createChart = (ctx, data, label) => {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: years.slice(1), // Remove the first year for percentage
                    datasets: Object.keys(data).map((key, index) => ({
                        label: key,
                        data: data[key],
                        borderColor: `hsl(${index * 40}, 70%, 50%)`,
                        fill: false
                    }))
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: { callbacks: { label: (context) => `${context.raw}%` } }
                    },
                    scales: { y: { beginAtZero: true, ticks: { callback: (value) => `${value}%` } } }
                }
            });
        };

        // Render Charts
        createChart(document.getElementById('monthlyChart'), percentageMonthly, "Monthly Performance");
        createChart(document.getElementById('sourceChart'), percentageSource, "Source Contributions");
        createChart(document.getElementById('typeChart'), percentageType, "Type Contributions");
    </script>
</body>
</html>
