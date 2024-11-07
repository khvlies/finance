<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kutipan Zakat</title>
    <link rel="stylesheet" href="css/overview.css">
</head>
<body>
    <div class="container">
        <a class="btn btn-secondary" href="kutipan_bulanan.php" role="button">BACK</a><h2>Kutipan Zakat</h2>
        <div class="scrollmenu">
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
    </div>
</body>
</html>
