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
        <h2>Kutipan Jenis Zakat</h2>
        <a class="btn btn-primary" href="#.php" role="button">OVERVIEW</a>
        <br>
        <table class="table">
            <thead>
                <tr>
                    <th>YEAR</th>
                    <th>VIEW</th>

                </tr>
            </thead>
            <tbody>
                    <?php
                    // Database credentials
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $database = "finstatdb2";

                    // Create connection
                    $connection = new mysqli($servername, $username, $password, $database);

                    // Check connection
                    if ($connection->connect_error) {
                        die("Connection failed: " . $connection->connect_error);
                    }

                    // SQL Query with Prepared Statement
                    $stmt = $connection->prepare("SELECT years, category_id, amount FROM kutipan_jenis");
                    $stmt->execute();
                    $result = $stmt->get_result();

                    // Category mapping
                    $typekutipan = [
                        33 => "Pendapatan", 34 => "Perniagaan", 35 => "Harta", 36 => "Simpanan",
                        37 => "Saham", 38 => "KWSP", 39 => "Tanaman", 40 => "Emas",
                        41 => "Ternakan", 42 => "Perak", 43 => "Fitrah"
                    ];

                    // Display results in table rows
                    while ($row = $result->fetch_assoc()) {
                        $zakatType = isset($typekutipan[(int)$row['category_id']]) 
                            ? $typekutipan[(int)$row['category_id']] 
                            : "Unknown";
                        $formattedAmount = number_format($row['amount']);
                        echo "<tr>
                            <td>{$row['years']}</td>
                            <td >
                                <a class="btn btn-secondary" href='#.php?study_id={$row['study_id']}'>Bulanan</a>
                            </td>
                        </tr>";
                    }

                    // Close connection
                    $stmt->close();
                    $connection->close();
                    ?>
                </tbody>
        </table>
    </div>
    </main>
</body>
</html>