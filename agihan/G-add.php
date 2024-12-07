<?php
include('../dbconn.php');

// Fetch categories for Jenis Kutipan and Sumber
$stmt_jenis = $dbconn->prepare("SELECT category_id, category_name FROM category WHERE category_type = 'agihan'");
$stmt_jenis->execute();
$result_jenis = $stmt_jenis->get_result();

$stmt_sumber = $dbconn->prepare("SELECT category_id, category_name FROM category WHERE category_type = 'asnaf'");
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
    <a class="btn btn-secondary" href="../agihan/A-agihan.php" role="button">BACK</a>
    <div class="container">
        <h2>ADD NEW DATA</h2>

        <!-- Dropdown to select the type of data to add -->
        <label for="data-type">Select Data Type:</label>
        <select id="data-type" name="data-type">
            <option value="">-- Select --</option>
            <option value="agihan">Kategori Agihan</option>
            <option value="asnaf">Kategori Asnaf</option>
        </select>

        <!-- Form for Jenis Kutipan -->
        <div id="agihan-form" class="form-section">
            <form action="insert_agihan.php" method="post">
                <h3>Kategori Agihan</h3>
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

                <button type="submit" class="btn-primary">Add Kategori Agihan</button>
            </form>
        </div>

        <!-- Form for Kutipan Sumber -->
        <div id="asnaf-form" class="form-section">
            <form action="insert_asnaf.php" method="post">
                <h3>Kategori Asnaf</h3>
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

                <button type="submit" class="btn-primary">Add Kategori Agihan</button>
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