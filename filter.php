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
        <!-- Filter Form -->
        <form method="GET" action="">
            <div>
                <label for="year">Year:</label>
                <select name="year" id="year">
                    <option value="">All</option>
                    <?php
                    // Assuming years range from 2020 to 2024 for example
                    for ($y = 2020; $y <= date("Y"); $y++) {
                        $selected = (isset($_GET['year']) && $_GET['year'] == $y) ? "selected" : "";
                        echo "<option value=\"$y\" $selected>$y</option>";
                    }
                    ?>
                </select>

                <label for="month">Month:</label>
                <select name="month" id="month">
                    <option value="">All</option>
                    <?php
                    $monthNames = [
                        1 => "JANUARI", 2 => "FEBRUARI", 3 => "MAC", 4 => "APRIL",
                        5 => "MEI", 6 => "JUN", 7 => "JULAI", 8 => "OGOS",
                        9 => "SEPTEMBER", 10 => "OKTOBER", 11 => "NOVEMBER", 12 => "DISEMBER"
                    ];
                    foreach ($monthNames as $num => $name) {
                        $selected = (isset($_GET['month']) && $_GET['month'] == $num) ? "selected" : "";
                        echo "<option value=\"$num\" $selected>$name</option>";
                    }
                    ?>
                </select>

                <label for="amount">Minimum Amount:</label>
                <input type="number" name="amount" id="amount" value="<?php echo isset($_GET['amount']) ? $_GET['amount'] : ''; ?>" placeholder="Enter amount">

                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>
        
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

                // Create connection
                $connection = new mysqli($servername, $username, $password, $database);

                // Check connection
                if ($connection->connect_error){
                    die("Connection failed: ". $connection->connect_error);
                }

                // Read all rows from database table with filtering
                $conditions = [];

                // Add conditions based on selected filters
                if (!empty($_GET['year'])) {
                    $conditions[] = "years = '" . $connection->real_escape_string($_GET['year']) . "'";
                }
                if (!empty($_GET['month'])) {
                    $conditions[] = "months = '" . $connection->real_escape_string($_GET['month']) . "'";
                }
                if (!empty($_GET['amount'])) {
                    $conditions[] = "amount >= '" . $connection->real_escape_string($_GET['amount']) . "'";
                }

                // Construct the SQL query with conditions
                $sql = "SELECT * FROM kutipan_bulanan";
                if (count($conditions) > 0) {
                    $sql .= " WHERE " . implode(" AND ", $conditions);
                }
                $result = $connection->query($sql);

                if (!$result){
                    die("Invalid query: ". $connection->error);
                }

                // Display data in table with formatted month names and comma-separated amounts
                while($row = $result->fetch_assoc()){
                    $monthName = $monthNames[(int)$row['months']];
                    $formattedAmount = number_format($row['amount']);
                    echo "<tr>
                        <td>{$row['years']}</td>
                        <td>{$monthName}</td>
                        <td>{$formattedAmount}</td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    </main>
</body>
</html>
