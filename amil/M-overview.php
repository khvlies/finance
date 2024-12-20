<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kewangan Amil</title>
    <link rel="stylesheet" href="../css/overview.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <a class="btn btn-secondary" href="../amil/A-amil.php" role="button">BACK</a>
        <a href="../report_M.php" class="btn btn-secondary" role="button">DOWNLOAD</a>

        <h2>Kewangan Amil</h2>
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
                        <th>Pendapatan Amil</th>
                        <?php
                        include('../dbconn.php');

                        // Get year range
                        $yearRangeResult = $dbconn->query("SELECT MIN(years) AS min_year, MAX(years) AS max_year FROM amil_income");
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
                        FROM amil_income m
                        JOIN category c ON m.category_id = c.category_id
                        WHERE c.category_type = 'pendapatan'
                    ");
                    $stmt->execute();
                    $incomeResult = $stmt->get_result();
                    
                    while ($income = $incomeResult->fetch_assoc()) {
                        $incomeName = $income['category_name'];
                        echo "<tr>";
                        echo "<td>$incomeName</td>";
                        for ($year = $minYear; $year <= $maxYear; $year++) {
                            $stmtTotal = $dbconn->prepare("
                                SELECT COALESCE(SUM(amount), 1) AS total
                                FROM amil_income
                                WHERE years = ?
                            ");
                            $stmtTotal->bind_param("i", $year);
                            $stmtTotal->execute();
                            $totalResult = $stmtTotal->get_result();
                            $total = $totalResult->fetch_assoc()['total'];

                            $stmtAmount = $dbconn->prepare("
                                SELECT COALESCE(SUM(m.amount), 0) AS amount
                                FROM amil_income m
                                JOIN category c ON m.category_id = c.category_id
                                WHERE m.years = ? AND c.category_name = ?
                            ");
                            $stmtAmount->bind_param("is", $year, $incomeName);
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
                            $stmtTotalIncomeYear = $dbconn->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM amil_income WHERE years = ?");
                            $stmtTotalIncomeYear->bind_param("i", $year);
                            $stmtTotalIncomeYear->execute();
                            $totalResult = $stmtTotalIncomeYear->get_result();
                            $yearTotal = $totalResult->fetch_assoc()['total'];
                            echo "<th data-absolute='" . number_format($yearTotal, 2) . "' data-percentage='-'>" . number_format($yearTotal, 2) . "</th>";
                            $stmtTotalIncomeYear->close();
                        }
                        ?>
                    </tr>
                </tfoot>
            </table>

            <table>
                <thead>
                    <tr>
                        <th>Perbelanjaan Amil</th>
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
                        FROM amil_expense m
                        JOIN category c ON m.category_id = c.category_id
                        WHERE c.category_type = 'perbelanjaan'
                    ");
                    $stmt->execute();
                    $expenseResult = $stmt->get_result();

                    while ($expense = $expenseResult->fetch_assoc()) {
                        $expenseName = $expense['category_name'];
                        echo "<tr>";
                        echo "<td>$expenseName</td>";
                        for ($year = $minYear; $year <= $maxYear; $year++) {
                            $stmtTotal = $dbconn->prepare("
                                SELECT COALESCE(SUM(amount), 1) AS total
                                FROM amil_expense
                                WHERE years = ?
                            ");
                            $stmtTotal->bind_param("i", $year);
                            $stmtTotal->execute();
                            $totalResult = $stmtTotal->get_result();
                            $total = $totalResult->fetch_assoc()['total'];

                            $stmtAmount = $dbconn->prepare("
                                SELECT COALESCE(SUM(m.amount), 0) AS amount
                                FROM amil_expense m
                                JOIN category c ON m.category_id = c.category_id
                                WHERE m.years = ? AND c.category_name = ?
                            ");
                            $stmtAmount->bind_param("is", $year, $expenseName);
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
                            $stmtTotalExpenseYear = $dbconn->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM amil_expense WHERE years = ?");
                            $stmtTotalExpenseYear->bind_param("i", $year);
                            $stmtTotalExpenseYear->execute();
                            $totalResult = $stmtTotalExpenseYear->get_result();
                            $yearTotal = $totalResult->fetch_assoc()['total'];
                            echo "<th data-absolute='" . number_format($yearTotal, 2) . "' data-percentage='-'>" . number_format($yearTotal, 2) . "</th>";
                            $stmtTotalExpenseYear->close();
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
        <h3>AMIL INCOMES</h3>
        <canvas id="incomeChart"></canvas>

        <h3>AMIL EXPENSES</h3>
        <canvas id="expenseChart"></canvas>

    </div>
    <?php
    $dataIncome = [];
    $dataExpense = [];

    $incomeResult = $dbconn->query("SELECT DISTINCT category_name FROM category WHERE category_type = 'pendapatan'");
    while ($income = $incomeResult->fetch_assoc()) {
        $sourceName = $income['category_name'];
        $dataIncome[$sourceName] = [];
        for ($year = $minYear; $year <= $maxYear; $year++) {
            $stmt = $dbconn->prepare("SELECT COALESCE(SUM(amount), 0) AS amount FROM amil_income m JOIN category c ON m.category_id = c.category_id WHERE m.years = ? AND c.category_name = ?");
            $stmt->bind_param("is", $year, $sourceName);
            $stmt->execute();
            $result = $stmt->get_result();
            $dataIncome[$sourceName][$year] = $result->fetch_assoc()['amount'] ?? 0;
            $stmt->close();
        }
    }

    $expenseResult = $dbconn->query("SELECT DISTINCT category_name FROM category WHERE category_type = 'perbelanjaan'");
    while ($expense = $expenseResult->fetch_assoc()) {
        $sourceName = $expense['category_name'];
        $dataExpense[$sourceName] = [];
        for ($year = $minYear; $year <= $maxYear; $year++) {
            $stmt = $dbconn->prepare("SELECT COALESCE(SUM(amount), 0) AS amount FROM amil_expense m JOIN category c ON m.category_id = c.category_id WHERE m.years = ? AND c.category_name = ?");
            $stmt->bind_param("is", $year, $sourceName);
            $stmt->execute();
            $result = $stmt->get_result();
            $dataExpense[$sourceName][$year] = $result->fetch_assoc()['amount'] ?? 0;
            $stmt->close();
        }
    }
    ?>

    <script>
        const chartYears = <?= json_encode(range(2016, $maxYear)); ?>;

        // Filtered data for Agihan
        const incomeDataFiltered = Object.fromEntries(
            Object.entries(<?= json_encode($dataIncome); ?>).map(([source, values]) => [
                source,
                chartYears.map((year) => values[year] || 0),
            ])
        );

        // Filtered data for Asnaf
        const expenseDataFiltered = Object.fromEntries(
            Object.entries(<?= json_encode($dataExpense); ?>).map(([source, values]) => [
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
        const percentageIncome = calculatePercentageIncrease(incomeDataFiltered);
        const percentageExpense = calculatePercentageIncrease(expenseDataFiltered);

        // Function to create a chart
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

        // Create charts
        createPercentageChart(document.getElementById('incomeChart'), percentageIncome, "Amil Income Percentage");
        createPercentageChart(document.getElementById('expenseChart'), percentageExpense, "Amil Expense Percentage");

    </script>    
    
</body>
</html>
