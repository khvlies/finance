<?php
include('../dbconn.php');

if (isset($_GET['year'])) {
    $year = $_GET['year'];

    // Fetch data for the selected year
    $stmt_bulanan = $dbconn->prepare("SELECT * FROM kutipan_bulanan WHERE years = ?");
    $stmt_bulanan->bind_param("i", $year);
    $stmt_bulanan->execute();
    $result_bulanan = $stmt_bulanan->get_result();

    $monthNames = [
        1 => "JANUARI", 2 => "FEBRUARI", 3 => "MAC", 4 => "APRIL",
        5 => "MEI", 6 => "JUN", 7 => "JULAI", 8 => "OGOS",
        9 => "SEPTEMBER", 10 => "OKTOBER", 11 => "NOVEMBER", 12 => "DISEMBER"
    ];

    $stmt_jenis = $dbconn->prepare("SELECT k.amount, k.category_id, c.category_name 
        FROM kutipan_jenis k
        JOIN category c ON k.category_id = c.category_id
        WHERE k.years = ? AND c.category_type = 'jenis kutipan'");
    $stmt_jenis->bind_param("i", $year);
    $stmt_jenis->execute();
    $result_jenis = $stmt_jenis->get_result();

    $stmt_sumber = $dbconn->prepare("SELECT COALESCE(k.amount, 0) AS amount, k.category_id, c.category_name 
        FROM kutipan_sumber k
        JOIN category c ON k.category_id = c.category_id
        WHERE k.years = ? AND c.category_type = 'sumber'");
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
    <title>Kutipan <?php echo $year; ?></title>
    <link rel="stylesheet" href="../css/edit.css">
</head>
<body>
<main>
    <a class="btn btn-secondary" href="../kutipan/A-kutipan.php" role="button">BACK</a>
    <div class="container">
        <form action="../kutipan/update.php" method="post">
            <input type="hidden" name="year" value="<?php echo $year; ?>">

            <!-- Kutipan Bulanan Section -->
            <h2 class="section-title">Kutipan Bulanan</h2>
            <table class="edit-table">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result_bulanan->fetch_assoc()) {
                        $monthName = $monthNames[(int)$row['months']]; ?>
                        <tr>
                            <td><?php echo $monthName; ?></td>
                            <td>
                                <input type="text" name="bulanan[<?php echo $row['months']; ?>]" value="<?php echo $row['amount']; ?>">
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <!-- Jenis Kutipan Section -->
            <h2 class="section-title">Jenis Kutipan</h2>
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
                                <input type="text" name="jenis[<?php echo $row['category_id']; ?>]" value="<?php echo $row['amount']; ?>">
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <!-- Kutipan Sumber Section -->
            <h2 class="section-title">Kutipan Sumber</h2>
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
                                <input type="text" name="sumber[<?php echo $row['category_id']; ?>]" value="<?php echo $row['amount']; ?>">
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
