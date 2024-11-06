<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kutipan Zakat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 14px;
        }
        th, td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: right;
        }
        th {
            background-color: #d0e4f1;
            color: #333;
        }
        td:first-child, th:first-child {
            text-align: left;
        }
        tfoot td {
            font-weight: bold;
            background-color: #e6f1f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Kutipan Zakat</h2>
        <table>
            <thead>
                <tr>
                    <th rowspan="2">PRESTASI BULANAN</th>
                    <?php
                    // Generate header years dynamically
                    for ($year = 2010; $year <= 2023; $year++) {
                        echo "<th>$year</th>";
                    }
                    ?>
                    <th rowspan="2">JUMLAH</th>
                </tr>
                
            </thead>
            <tbody>
                <?php
                $servername = "localhost";
                $username = "root";
                $password = "";
                $database = "finstatdb2";

                // Connect to database
                $connection = new mysqli($servername, $username, $password, $database);
                if ($connection->connect_error) {
                    die("Connection failed: " . $connection->connect_error);
                }

                // Define month mapping
                $monthNames = [
                    1 => "JANUARI", 2 => "FEBRUARI", 3 => "MAC", 4 => "APRIL",
                    5 => "MEI", 6 => "JUN", 7 => "JULAI", 8 => "OGOS",
                    9 => "SEPTEMBER", 10 => "OKTOBER", 11 => "NOVEMBER", 12 => "DISEMBER"
                ];

                // Retrieve monthly data for each month in integer order
                foreach ($monthNames as $monthNumber => $monthName) {
                    echo "<tr>";
                    echo "<td>$monthName</td>";
                    $total = 0;
                    for ($year = 2010; $year <= 2023; $year++) {
                        $sql = "SELECT amount FROM kutipan_bulanan WHERE months='$monthNumber' AND years=$year";
                        $result = $connection->query($sql);
                        if ($result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                            $amount = $row['amount'];
                            echo "<td>" . number_format($amount, 2) . "</td>";
                            $total += $amount;
                        } else {
                            echo "<td>0.00</td>";
                        }
                    }
                    echo "<td>" . number_format($total, 2) . "</td>";
                    echo "</tr>";
                }

                // Retrieve total for each year
                echo "<tfoot><tr><td>JUMLAH KUTIPAN ZAKAT</td>";
                $grandTotal = 0;
                for ($year = 2010; $year <= 2023; $year++) {
                    $sql = "SELECT SUM(amount) AS year_total FROM kutipan_bulanan WHERE years=$year";
                    $result = $connection->query($sql);
                    $yearTotal = ($result->num_rows > 0) ? $result->fetch_assoc()['year_total'] : 0;
                    echo "<td>" . number_format($yearTotal, 2) . "</td>";
                    $grandTotal += $yearTotal;
                }
                echo "<td>" . number_format($grandTotal, 2) . "</td></tr></tfoot>";

                $connection->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
