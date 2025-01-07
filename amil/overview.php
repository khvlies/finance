<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/f-logo.png"  type="image/png">
    <title>Kewangan Amil</title>
    <link rel="stylesheet" href="../css/overview.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <a class="btn btn-secondary" href="../amil/amilMain.php" role="button">BACK</a>
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
                        <th>TOTAL EXPENSE</th>
                        <?php
                        for ($year = $minYear; $year <= $maxYear; $year++) {
                            $stmtTotalExpenseYear = $dbconn->prepare("SELECT COALESCE(SUM(CASE WHEN category_id IN (15, 16, 17, 18, 19, 20, 21, 22, 25) THEN amount ELSE 0 END), 0) AS total_expense FROM amil_expense WHERE years = ?");
                            $stmtTotalExpenseYear->bind_param("i", $year);
                            $stmtTotalExpenseYear->execute();
                            $totalResult = $stmtTotalExpenseYear->get_result();
                            $yearTotal = $totalResult->fetch_assoc()['total_expense'];
                            echo "<th data-absolute='" . number_format($yearTotal, 2) . "' data-percentage='-'>" . number_format($yearTotal, 2) . "</th>";
                            $stmtTotalExpenseYear->close();
                        }
                        ?>
                    </tr>
                    <tr>
                        <th>EXCESS AMOUNT</th>
                        <?php
                        for ($year = $minYear; $year <= $maxYear; $year++) {
                            // Fetch the total income for the year
                            $stmtTotalIncome = $dbconn->prepare("SELECT COALESCE(SUM(amount), 0) AS total_income FROM amil_income WHERE years = ?");
                            $stmtTotalIncome->bind_param("i", $year);
                            $stmtTotalIncome->execute();
                            $incomeResult = $stmtTotalIncome->get_result();
                            $totalIncome = $incomeResult->fetch_assoc()['total_income'] ?? 0;

                            // Fetch the specific expense for category_id 23
                            $stmtSpecificExpense = $dbconn->prepare("SELECT COALESCE(SUM(amount), 0) AS specific_expense FROM amil_expense WHERE years = ? AND category_id = 23");
                            $stmtSpecificExpense->bind_param("i", $year);
                            $stmtSpecificExpense->execute();
                            $specificExpenseResult = $stmtSpecificExpense->get_result();
                            $specificExpense = $specificExpenseResult->fetch_assoc()['specific_expense'] ?? 0;

                            // Calculate Excess Amount: Total Income - (Total Expense + Specific Expense)
                            $excessAmount = $totalIncome - ($yearTotal + $specificExpense);

                            echo "<th data-absolute='" . number_format($excessAmount, 2) . "' data-percentage='-'>" . number_format($excessAmount, 2) . "</th>";

                            $stmtTotalIncome->close();
                            $stmtSpecificExpense->close();
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
    const chartYears = <?= json_encode(range($minYear, $maxYear)); ?>;

    // Filter data for income and expense
    const incomeDataFiltered = Object.fromEntries(
        Object.entries(<?= json_encode($dataIncome); ?>).map(([source, values]) => [
            source,
            chartYears.map((year) => values[year] || 0),
        ])
    );

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

    const percentageIncome = calculatePercentageIncrease(incomeDataFiltered);
    const percentageExpense = calculatePercentageIncrease(expenseDataFiltered);

    // Create a chart with amount and percentage
    const createChart = (ctx, data, percentageData, label) => {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartYears,
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

    // Create income and expense charts
    createChart(
        document.getElementById('incomeChart'),
        incomeDataFiltered,
        percentageIncome,
        "Amil Income"
    );

    createChart(
        document.getElementById('expenseChart'),
        expenseDataFiltered,
        percentageExpense,
        "Amil Expense"
    );
</script>    
    
</body>
</html>
