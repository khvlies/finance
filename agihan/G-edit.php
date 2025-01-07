<?php
include('../dbconn.php');

if (isset($_GET['year'])) {
    $year = $_GET['year'];

    // Fetch data for the selected year

    $stmt_jenis = $dbconn->prepare("SELECT a.amount, a.category_id, c.category_name 
        FROM agihan_category a
        JOIN category c ON a.category_id = c.category_id
        WHERE a.years = ? AND c.category_type = 'agihan'");
    $stmt_jenis->bind_param("i", $year);
    $stmt_jenis->execute();
    $result_jenis = $stmt_jenis->get_result();

    $stmt_sumber = $dbconn->prepare("SELECT COALESCE(a.amount, 0) AS amount, a.category_id, c.category_name 
        FROM agihan_asnaf a
        JOIN category c ON a.category_id = c.category_id
        WHERE a.years = ? AND c.category_type = 'asnaf'");
    $stmt_sumber->bind_param("i", $year);
    $stmt_sumber->execute();
    $result_sumber = $stmt_sumber->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/f-logo.png"  type="image/png">
    <title>Kutipan <?php echo $year; ?></title>
    <link rel="stylesheet" href="../css/edit.css">
</head>
<body>
<main>
    <a class="btn btn-secondary" href="../agihan/A-agihan.php" role="button">BACK</a>
    <div class="container">
        <form action="../agihan/G-update.php" method="post">
            <input type="hidden" name="year" value="<?php echo $year; ?>">

            <!-- Jenis Kutipan Section -->
            <h2 class="section-title">Kategori Agihan</h2>
            <table class="edit-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result_jenis->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['category_name']; ?></td>
                            <td>
                                <input type="text" name="agihan[<?php echo $row['category_id']; ?>]" value="<?php echo $row['amount']; ?>">
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <!-- Kutipan Sumber Section -->
            <h2 class="section-title">Kategori Asnaf</h2>
            <table class="edit-table">
                <thead>
                    <tr>
                        <th>Source</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result_sumber->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['category_name']; ?></td>
                            <td>
                                <input type="text" name="asnaf[<?php echo $row['category_id']; ?>]" value="<?php echo $row['amount']; ?>">
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <!-- Submit Button -->
            <button type="submit" class="btn-primary">Save Changes</button>
        </form>
    </div>
</main>
</body>
</html>
