<?php
include('../dbconn.php');

// Fetch categories for Jenis Kutipan and Sumber
$stmt_income = $dbconn->prepare("SELECT category_id, category_name FROM category WHERE category_type = 'pendapatan'");
$stmt_income->execute();
$result_income = $stmt_income->get_result();

$stmt_expense = $dbconn->prepare("SELECT category_id, category_name FROM category WHERE category_type = 'perbelanjaan'");
$stmt_expense->execute();
$result_expense = $stmt_expense->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/f-logo.png"  type="image/png">
    <title>Add Data</title>
    <link rel="stylesheet" href="../css/add.css">
    
</head>
<body>
    <a class="btn btn-secondary" href="../amil/A-amil.php" role="button">BACK</a>
    <div class="container">
        <h2>ADD NEW DATA</h2>

        <!-- Dropdown to select the type of data to add -->
        <label for="data-type">Select Data Type:</label>
        <select id="data-type" name="data-type">
            <option value="">-- Select --</option>
            <option value="pendapatan">Pendapatan Amil</option>
            <option value="perbelanjaan">Perbelanjaan Amil</option>
        </select>

        <!-- Form for Pendapatan -->
        <div id="pendapatan-form" class="form-section">
            <form action="insert_pendapatan.php" method="post">
                <h3>Pendapatan</h3>
                <label for="year">Year:</label>
                <select id="year" name="year" required>
                <?php
                $startYear = 2009;
                $endYear = date("Y"); // Use the current year as the end year

                for ($year = $startYear; $year <= $endYear; $year++) {
                    echo "<option value=\"$year\">$year</option>";
                }
                ?>
                </select>

                <label for="category">Category:</label>
                <select id="category" name="category_id" required>
                    <option value="">-- Select Category --</option>
                    <?php while ($row = $result_income->fetch_assoc()) { ?>
                        <option value="<?php echo $row['category_id']; ?>">
                            <?php echo $row['category_name']; ?>
                        </option>
                    <?php } ?>
                </select>

                <label for="amount">Amount:</label>
                <input type="number" id="amount" name="amount" required>

                <button type="submit" class="btn-primary">Add Pendapatan</button>
            </form>
        </div>

        <!-- Form for Agihan Asnaf -->
        <div id="perbelanjaan-form" class="form-section">
            <form action="insert_perbelanjaan.php" method="post">
                <h3>Perbelanjaan Amil</h3>
                <label for="year">Year:</label>
                <select id="year" name="year" required>
                <?php
                $startYear = 2009;
                $endYear = date("Y");

                for ($year = $startYear; $year <= $endYear; $year++) {
                    echo "<option value=\"$year\">$year</option>";
                }
                ?>
                </select>
                <label for="category">Category:</label>
                <select id="category" name="category_id" required>
                    <option value="">-- Select Category --</option>
                    <?php while ($row = $result_expense->fetch_assoc()) { ?>
                        <option value="<?php echo $row['category_id']; ?>">
                            <?php echo $row['category_name']; ?>
                        </option>
                    <?php } ?>
                </select>

                <label for="amount">Amount:</label>
                <input type="number" id="amount" name="amount" required>

                <button type="submit" class="btn-primary">Add Perbelanjaan</button>
            </form>
        </div>
    </div>

    <script>
        
        document.getElementById('data-type').addEventListener('change', function () {
            const selectedValue = this.value;

           
            document.querySelectorAll('.form-section').forEach(section => {
                section.classList.remove('active');
            });

           
            if (selectedValue) {
                document.getElementById(selectedValue + '-form').classList.add('active');
            }
        });
    </script>
</body>
</html>
