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
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $database = "finstatdb2";

                    // Connect to database
                    $connection = new mysqli($servername, $username, $password, $database);
                    if ($connection->connect_error) {
                        die("Connection failed: " . $connection->connect_error);
                    }

                    // Get the range of years dynamically from the database
                    $yearRangeResult = $connection->query("SELECT MIN(years) AS min_year, MAX(years) AS max_year FROM kutipan_bulanan");
                    $yearRange = $yearRangeResult->fetch_assoc();
                    $minYear = $yearRange['min_year'];
                    $maxYear = $yearRange['max_year'];

                    // Generate header years based on the range from minYear to maxYear
                    for ($year = $minYear; $year <= $maxYear; $year++) {
                        echo "<th>$year</th>";
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                // Define month mapping
                $monthNames = [
                    1 => "JANUARI", 2 => "FEBRUARI", 3 => "MAC", 4 => "APRIL",
                    5 => "MEI", 6 => "JUN", 7 => "JULAI", 8 => "OGOS",
                    9 => "SEPTEMBER", 10 => "OKTOBER", 11 => "NOVEMBER", 12 => "DISEMBER"
                ];

                // Loop through each month and create a row
                foreach ($monthNames as $monthNumber => $monthName) {
                    echo "<tr>";
                    echo "<td>$monthName</td>";
                    for ($year = $minYear; $year <= $maxYear; $year++) {
                        // Get the amount for the current month and year
                        $sql = "SELECT amount FROM kutipan_bulanan WHERE months='$monthNumber' AND years=$year";
                        $result = $connection->query($sql);
                        if ($result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                            $amount = $row['amount'];
                            echo "<td>" . number_format($amount, 2) . "</td>";
                        } else {
                            echo "<td>0.00</td>";
                        }
                    }
                    echo "</tr>";
                }

                // Retrieve total for each year
                echo "<tfoot><tr><td>JUMLAH KUTIPAN ZAKAT</td>";
                for ($year = $minYear; $year <= $maxYear; $year++) {
                    $sql = "SELECT SUM(amount) AS year_total FROM kutipan_bulanan WHERE years=$year";
                    $result = $connection->query($sql);
                    $yearTotal = ($result->num_rows > 0) ? $result->fetch_assoc()['year_total'] : 0;
                    echo "<td>" . number_format($yearTotal, 2) . "</td>";
                }
                echo "</tr></tfoot>";

                $connection->close();
                ?>
            </tbody>
        </table>
        </div>
    </div>
</body>
</html>
