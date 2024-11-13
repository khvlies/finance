<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/icon.png"/>
    <title>Kutipan</title>
    <link rel="stylesheet" href="css/mainview.css">
</head>
<body>
<?php include('navigation.php'); ?>
    <main>
    <div class="container my-5">
        <h2>Kutipan Zakat Bulanan</h2>
        <a class="btn btn-primary" href="viewbulanan.php" role="button">OVERVIEW</a>
        <br>
        <table class="table">
            <thead>
                <tr>
                    <th>YEAR</th>
                    <th>MONTH</th>
                    <th>AMOUNT</th>
                    

                </tr>
            </thead>
            <tbody>
                <?php
                $servername = "localhost";
                $username = "root";
                $password = "";
                $database = "finstatdb2";

                //create connection
                $connection = new mysqli($servername, $username, $password, $database);

                //check connection
                if ($connection->connect_error){
                    die("Connection failed: ". $connection->connect_error);
                }

                //read all row from database table
                $sql = "SELECT * FROM kutipan_bulanan";
                $result = $connection->query($sql);

                if (!$result){
                    die("Invalid query: ". $connection->error);
                }
                // Define month mapping
                $monthNames = [
                    1 => "JANUARI", 2 => "FEBRUARI", 3 => "MAC", 4 => "APRIL",
                    5 => "MEI", 6 => "JUN", 7 => "JULAI", 8 => "OGOS",
                    9 => "SEPTEMBER", 10 => "OKTOBER", 11 => "NOVEMBER", 12 => "DISEMBER"
                ];
                //read data of each row
                while($row = $result->fetch_assoc()){
                    $monthName = $monthNames[(int)$row['months']];
                    $formattedAmount = number_format($row['amount']);

                    echo "<tr>
                    <td>$row[years]</td>
                    <td>$monthName</td>
                    <td>$formattedAmount</td>
                    
                </tr>
                ";
                }
                ?>
            </tbody>
        </table>
    </div>
    </main>
</body>
</html>