<?php
// Include database connection
include 'dbconn.php';

// Start of HTML document
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kutipan Bulanan</title>
    <link rel="stylesheet" href="css/test.css">
</head>
<body>
    <div class="container">
        <h1>Kutipan Bulanan</h1>
        
        <!-- Data table -->
        <table>
            <thead>
                <tr>
                    <th>Tahun</th>
                    <th>JAN</th>
                    <th>FEB</th>
                    <th>MAC</th>
                    <th>APR</th>
                    <th>MEI</th>
                    <th>JUN</th>
                    <th>JUL</th>
                    <th>OGOS</th>
                    <th>SEP</th>
                    <th>OKT</th>
                    <th>NOV</th>
                    <th>DIS</th>
                    <th>Jumlah Kutipan (RM)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // SQL query to fetch monthly data
                $sql = "SELECT Tahun, JAN, FEB, MAC, APR, MEI, JUN, JUL, OGOS, SEP, OKT, NOV, DIS, jumlah_kutipan 
                        FROM kutipan_bulanan 
                        ORDER BY Tahun";
                
                // Execute the query
                $result = $dbconn->query($sql);

                // Check and display data if available
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['Tahun']) . "</td>";
                        echo "<td>" . number_format($row['JAN'], 2) . "</td>";
                        echo "<td>" . number_format($row['FEB'], 2) . "</td>";
                        echo "<td>" . number_format($row['MAC'], 2) . "</td>";
                        echo "<td>" . number_format($row['APR'], 2) . "</td>";
                        echo "<td>" . number_format($row['MEI'], 2) . "</td>";
                        echo "<td>" . number_format($row['JUN'], 2) . "</td>";
                        echo "<td>" . number_format($row['JUL'], 2) . "</td>";
                        echo "<td>" . number_format($row['OGOS'], 2) . "</td>";
                        echo "<td>" . number_format($row['SEP'], 2) . "</td>";
                        echo "<td>" . number_format($row['OKT'], 2) . "</td>";
                        echo "<td>" . number_format($row['NOV'], 2) . "</td>";
                        echo "<td>" . number_format($row['DIS'], 2) . "</td>";
                        echo "<td>" . number_format($row['jumlah_kutipan'], 2) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    // Display message if no data is available
                    echo "<tr><td colspan='14'>No data available</td></tr>";
                }

                // Close the database connection
                $dbconn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
