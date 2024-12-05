<?php
include('../dbconn.php');

// Fetch categories for Jenis Kutipan and Sumber
$stmt_jenis = $dbconn->prepare("SELECT category_id, category_name FROM category WHERE category_type = 'jenis kutipan'");
$stmt_jenis->execute();
$result_jenis = $stmt_jenis->get_result();

$stmt_sumber = $dbconn->prepare("SELECT category_id, category_name FROM category WHERE category_type = 'sumber'");
$stmt_sumber->execute();
$result_sumber = $stmt_sumber->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Data</title>
    <link rel="stylesheet" href="../css/add.css">
    
</head>
<body>
    <a class="btn btn-secondary" href="../kutipan/A-kutipan.php" role="button">BACK</a>
    <div class="container">
        <h2>ADD NEW DATA</h2>

        <!-- Dropdown to select the type of data to add -->
        <label for="data-type">Select Data Type:</label>
        <select id="data-type" name="data-type">
            <option value="">-- Select --</option>
            <option value="bulanan">Kutipan Bulanan</option>
            <option value="jenis">Jenis Kutipan</option>
            <option value="sumber">Kutipan Sumber</option>
        </select>

        <!-- Form for Kutipan Bulanan -->
        <div id="bulanan-form" class="form-section">
            <form action="insert_bulanan.php" method="post">
                <h3>Kutipan Bulanan</h3>
                <label for="year">Year:</label>
                <input type="number" id="year" name="year" required>

                <label for="month">Month:</label>
                <select id="month" name="month" required>
                    <option value="1">January</option>
                    <option value="2">February</option>
                    <option value="3">March</option>
                    <option value="4">April</option>
                    <option value="5">May</option>
                    <option value="6">June</option>
                    <option value="7">July</option>
                    <option value="8">August</option>
                    <option value="9">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>

                <label for="amount">Amount:</label>
                <input type="number" id="amount" name="amount" required>

                <button type="submit" class="btn-primary">Add Kutipan Bulanan</button>
            </form>
        </div>

        <!-- Form for Jenis Kutipan -->
        <div id="jenis-form" class="form-section">
            <form action="insert_jenis.php" method="post">
                <h3>Jenis Kutipan</h3>
                <label for="year">Year:</label>
                <input type="number" id="year" name="year" required>

                <label for="category">Category:</label>
                <select id="category" name="category_id" required>
                    <option value="">-- Select Category --</option>
                    <?php while ($row = $result_jenis->fetch_assoc()) { ?>
                        <option value="<?php echo $row['category_id']; ?>">
                            <?php echo $row['category_name']; ?>
                        </option>
                    <?php } ?>
                </select>

                <label for="amount">Amount:</label>
                <input type="number" id="amount" name="amount" required>

                <button type="submit" class="btn-primary">Add Jenis Kutipan</button>
            </form>
        </div>

        <!-- Form for Kutipan Sumber -->
        <div id="sumber-form" class="form-section">
            <form action="insert_sumber.php" method="post">
                <h3>Kutipan Sumber</h3>
                <label for="year">Year:</label>
                <input type="number" id="year" name="year" required>

                <label for="category">Category:</label>
                <select id="category" name="category_id" required>
                    <option value="">-- Select Category --</option>
                    <?php while ($row = $result_sumber->fetch_assoc()) { ?>
                        <option value="<?php echo $row['category_id']; ?>">
                            <?php echo $row['category_name']; ?>
                        </option>
                    <?php } ?>
                </select>

                <label for="amount">Amount:</label>
                <input type="number" id="amount" name="amount" required>

                <button type="submit" class="btn-primary">Add Kutipan Sumber</button>
            </form>
        </div>
    </div>

    <script>
        // JavaScript to handle form visibility
        document.getElementById('data-type').addEventListener('change', function () {
            const selectedValue = this.value;

            // Hide all forms
            document.querySelectorAll('.form-section').forEach(section => {
                section.classList.remove('active');
            });

            // Show the selected form
            if (selectedValue) {
                document.getElementById(selectedValue + '-form').classList.add('active');
            }
        });
    </script>
</body>
</html>
